<?php
    class Payment extends CI_Model {
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
            return array($errors, $form);
        }
        public function make_payment($post) {
            $this->load->model('Database');
            $this->load->model('Catalogue');
            $data = $this->Database->get_cart_records();
            $cart_totals = $this->Catalogue->get_cart_totals($data);
            require_once('application/libraries/stripe-php/init.php');
            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret'));
            $charge = \Stripe\Charge::create ([
                    "amount" => intval(str_replace('.', '', $cart_totals['grand_total'])),
                    "currency" => "usd",
                    "source" => $post['stripeToken'],
                    "description" => "Dummy stripe payment." 
            ]);
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
            return $charge;
        }
        public function add_details($post) {
            /* Add checkout details to database */
            $fields = array('first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'zip_code');
            $prefix = array('ship_', 'bill_');
            $type = array(1,2);
            if (array_key_exists('type', $post)) {
                $prefix = array('ship_');
                $type = array(0);
            }
            $checkout = array();
            $ids = array();
            foreach ($prefix as $key => $pre) {
                $salt = bin2hex(openssl_random_pseudo_bytes(5));
                foreach ($fields as $field) {
                    $checkout[str_replace('_code', '', $field)] = md5($post[$pre . $field] . '' . $salt);
                }
                $checkout['user_id'] = $this->session->userdata('user')['id'];
                $checkout['salt'] = $salt;
                $checkout['type'] = $type[$key];
                array_push($ids, $this->Database->add_record('checkout_details', $checkout));
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
            $salt = bin2hex(openssl_random_pseudo_bytes(10));
            $card = array('salt' => $salt, 'user_id' => $this->session->userdata('user')['id']);
            foreach ($fields as $field => $val) {
                $card[$field] = md5($post[$val] . '' . $salt);
            }
            $this->Database->add_record('user_cards', $card);
            /* Delete cart_items */
            $this->Database->delete_record('cart_items', array('user_id' => $this->session->userdata('user')['id']));
            /* END */
            return 'success';
        }
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