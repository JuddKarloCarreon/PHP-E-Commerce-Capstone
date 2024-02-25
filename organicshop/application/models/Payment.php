<?php
    class Payment extends CI_Model {
        /* Validates the checkout shipping and billing info */
        public function validate_checkout($post) {
            $fields = array('first_name', 'last_name', 'address_1', 'address_2', 'city', 'state');
            $prefix = array('ship_', 'bill_');
            if (array_key_exists('type', $post)) {
                $prefix = array('ship_');
            }
            $this->load->library('form_validation');
            $do = $this->form_validation;
            $do->set_data($post);
            $do->set_error_delimiters('<p class="errors">','</p>');
            foreach ($prefix as $pre) {
                foreach ($fields as $field) {
                    $do->set_rules($pre . $field, str_replace('_', '', $field), 'trim|required');
                }
                $do->set_rules($pre . 'zip_code', 'zip code', 'trim|required|integer');
            }
            $errors = array('ship' => array(), 'bill' => array());
            $form = array('ship' => array(), 'bill' => array());
            array_push($fields, 'zip_code');
            if ($do->run() === FALSE) {
                foreach ($prefix as $pre) {
                    foreach ($fields as $field) {
                        $errors[str_replace('_', '', $pre)][$pre . $field] = form_error($pre . $field);
                        $form[str_replace('_', '', $pre)][$pre . $field] = $post[$pre . $field];
                    }
                }
            }
            /* $form is for repopulation purposes */
            return array($errors, $form);
        }
        /* Function that handles making the payment via stripe */
        public function make_payment($post) {
            $this->load->model('Database');
            $this->load->model('Catalogue');
            $data = $this->Database->get_cart_records();
            $cart_totals = $this->Catalogue->get_cart_totals($data);
            require_once('application/libraries/stripe-php/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret'));
            try { 
                // Charge a credit or a debit card 
                $charge = \Stripe\Charge::create (array(
                    "amount" => intval(str_replace('.', '', $cart_totals['grand_total'])),
                    "currency" => "usd",
                    "source" => $post['stripeToken'],
                    "description" => "Dummy stripe payment." 
                ));
                if ($charge) {
                    if ($charge['status'] == 'succeeded') {
                        /* Update sold */
                        foreach ($post['id'] as $key => $id) {
                            $record = $this->Database->get_record('products', 'id', $id);
                            $new_sold = intval($record['sold']) + intval($post['amount'][$key]);
                            $this->Database->update_record('products', $id, array('sold' => $new_sold));
                        }
                        return $charge['status'];
                    }
                }
            } catch(Exception $e) { 
                /* Return stock if payment didn't go through */
                foreach ($post['id'] as $key => $id) {
                    $record = $this->Database->get_record('products', 'id', $id);
                    $new_stock = intval($record['stock']) + intval($post['amount'][$key]);
                    $this->Database->update_record('products', $id, array('stock' => $new_stock));
                }
                return $e->getMessage();
            }             
        }
        /* Function that adds the necessary details to the database after all validations are successful */
        public function add_details($post) {
            $this->load->model('Database');
            /* Add checkout details to database */
            $fields = array('first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'zip_code');
            $prefix = array('ship_', 'bill_');
            if (array_key_exists('type', $post)) {
                $prefix = array('ship_');
            }
            $ids = array();
            $matching = TRUE;
            foreach ($prefix as $key => $pre) {
                $checkout = array();
                foreach ($fields as $field) {
                    $checkout[str_replace('_code', '', $field)] = $post[$pre . $field];
                }
                /* Check if record already exists before adding */
                $record = $this->Database->get_record('checkout_details', array_keys($checkout), array_values($checkout));
                if ($record === NULL) {
                    array_push($ids, $this->Database->add_record('checkout_details', $checkout));
                } else {
                    array_push($ids, $record['id']);
                }
            }
            /* End of checkout details, start of order details */
            if (count($ids) == 1) {
                array_push($ids, $ids[0]);
            }
            $products = array();
            foreach ($post['id'] as $key => $val) {
                $products[$val] = $post['amount'][$key];
            }
            $this->load->model('Catalogue');
            $order = array(
                'user_id' => $this->session->userdata('user')['id'],
                'shipping_id' => $ids[0],
                'billing_id' => $ids[1],
                'products_json' => json_encode($products),
                'total_amount' => $this->Catalogue->get_cart_totals($this->Database->get_cart_records())['grand_total']
            );
            $this->Database->add_record('orders', $order);
            /* End of order details, start of user cards */
            $fields = array('name' => 'card_name', 'number' => 'card_number', 'expiration' => 'expiration');
            $card = array('user_id' => $this->session->userdata('user')['id']);
            foreach ($fields as $field => $val) {
                $card[$field] = $post[$val];
            }
            /* Add to database if it doesn't exist yet */
            $record = $this->Database->get_record('user_cards', array_keys($card), array_values($card));
            if ($record === NULL) {
                $this->Database->add_record('user_cards', $card);
            }
            /* Delete cart_items */
            $this->Database->delete_record('cart_items', array('user_id' => $this->session->userdata('user')['id']));
            /* END */
            return 'success';
        }
        /* Checks the amount stock, then reduces the requested amount. Used when user is checking out */
        public function check_stock($post) {
            $this->load->model('Database');
            $error = 'Item in cart is out of stock.';
            $stock = array();
            foreach ($post['id'] as $key => $id) {
                $record = $this->Database->get_record('products', 'id', $id);
                if ($record === NULL) {
                    return $error;
                }
                if (intval($post['amount'][$key]) > intval($record['stock'])) {
                    return $error;
                }
                array_push($stock, intval($record['stock']));
            }
            foreach ($post['id'] as $key => $id) {
                $new_stock = $stock[$key] - intval($post['amount'][$key]);
                $this->Database->update_record('products', $id, array('stock' => $new_stock));
            }
            return 'success';
        }
    }
?>