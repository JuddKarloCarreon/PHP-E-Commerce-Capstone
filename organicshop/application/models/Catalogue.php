<?php
    class Catalogue extends CI_Model {
        /* Loads the models for all functions here */
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Database');
        }
        /* Function to obtain the parameters to pass to the view catalogue file */
        public function get_param() {
            $prod_count = array('All Products' => array($this->Database->count_records('products'), 0));
            foreach ($this->Database->product_types as $key => $val) {
                $prod_count[$val] = array($this->Database->count_records('products','product_type', $key + 1), $key + 1);
            }
            $this->session->flashdata('data'); /* To unset data from the admin_products if used */
            $data = $this->session->flashdata('cat_data');
            if ($data === NULL) {
                $data = $this->get_products();
            }
            $page = $this->session->flashdata('page');
            if ($page === NULL) {
                $page = $this->General->get_page_param();
            }
            $param = $this->General->get_base_param();
            return array_merge($param, array(
                'prod_count' => $prod_count,
                'data' => $data,
                'page' => $page,
                'hide_pages' => NULL,
                'search_val' => '',
                'cart_count' => $this->count_cart()
            ));
        }
        /* Function to obtain the parameters to pass to the product_view view file */
        public function get_product_param($id) {
            $this->load->model('User');
            $main_data = $this->get_product($id);
            $data = $this->get_products($main_data['product_type'], $main_data['id']);
            return array(
                'user' => $this->session->userdata('user'),
                'csrf' => $this->General->get_csrf(),
                'main_data' => $main_data,
                'data' => $data,
                'hide_pages' => '',
                'cart_count' => $this->count_cart(),
                'reviews' => $this->User->get_reviews($main_data['id'])
            );
        }
        /* Function to obtain the product data for the product_view view file */
        public function get_products($type = 0, $not_id = 0, $page = 1, $lim = '', $search = '') {
            $this->load->model('Database');
            $type = $this->Database->validate_id($type);
            $data = array();
            if ($type !== FALSE) {
                /* Excludes currently viewed product for the 'similar items' area */
                $not_field = 1;
                if ($not_id != 0) {
                    $not_field = 'id';
                }
                $field = 1;
                if ($type != 0) {
                    $field = 'product_type';
                }
                /* Obtain the data for the 'similar items' area and set the required information */
                $data = $this->Database->get_records('products', $field, $type, $not_field, $not_id, $page, $lim, 'name', "%$search%");
                foreach ($data as $key => $row) {
                    if (!in_array($row['image_names_json'], array('null', '[]', NULL, '[null]'))) {
                        $data[$key]['main_img'] = json_decode($row['image_names_json'])[0];
                    } else {
                        $data[$key]['main_img'] = '';
                    }
                    $data[$key]['category'] = $this->Database->product_types[intval($row['product_type']) - 1];
                    /* For the star rating */
                    $data[$key]['full'] = intval($row['rating'][0]);
                    $data[$key]['half'] = 0;
                    if (intval($row['rating'][2]) >= 5) {
                        $data[$key]['half'] = 1;
                    }
                    $data[$key]['empty'] = 5 - ($data[$key]['full'] + $data[$key]['half']);
                    $data[$key]['rating_count'] = $this->Database->count_records('reviews', 'product_id', $row['id']);
                }
            }
            $this->session->set_flashdata('data', $data);
            return $data;
        }
        /* First obtains data, then passes it to the search_products function in the general model */
        public function search($post, $data = 'none') {
            if ($data === 'none') {
                $data = $this->get_products();
            }
            return $this->General->search_products($post, $data);
        }
        /* Obtains the main product data for the product_view */
        public function get_product($id) {
            $data = $this->Database->get_record('products', 'id', $id);
            $data['images'] = json_decode($data['image_names_json']);
            foreach ($data['images'] as $key => $val) {
                $data['images'][$key] = 'products/' . $data['id'] . '/' . $val;
            }
            while (count($data['images']) < 4) {
                array_push($data['images'], 'close.svg');
            }
            $data['full'] = intval($data['rating'][0]);
                $data['half'] = 0;
                if (intval($data['rating'][2]) >= 5) {
                    $data['half'] = 1;
                }
                $data['empty'] = 5 - ($data['full'] + $data['half']);
                $data['rating_count'] = $this->Database->count_records('reviews', 'product_id', $data['id']);
            return $data;
        }
        /* Processes the handling of the database processes when adding to the cart */
        public function add_cart($post, $process = 'add') {
            /* Validation */
            $check = 'Product does not exist.';
            $id = $this->Database->validate_id($post['id']);
            if ($id === FALSE) {
                return $check;
            }
            $amount = intval($post['amount']);
            $record = $this->Database->get_record('products', 'id', $id);
            if ($record !== NULL) {
                if (intval($record['stock']) >= $amount && $amount > 0) {
                    $check = TRUE;
                }
            }
            /* Add to cart */
            if ($check === TRUE) {
                /* Check if user exists */
                $user = $this->session->userdata('user');
                if (empty($user)) {
                    return 'You must login before adding to cart.';
                }
                /* Check if item is already in cart */
                $cart_item = $this->Database->get_record('cart_items', array('user_id', 'product_id'), array($user['id'], $id));
                /* Set final amount */
                $final_amt = $amount;
                if ($cart_item !== NULL && $process == 'add') {
                    $final_amt = intval($cart_item['amount']) + $amount;
                }
                if ($cart_item === NULL) {
                    $data = array(
                        'user_id' => $user['id'],
                        'product_id' => $id,
                        'amount' => $amount
                    );
                    $this->Database->add_record('cart_items', $data);
                    return 'Successfully added to cart.';
                } else if ($final_amt > intval($record['stock'])) {
                    return 'Already reached stock limit.';
                } else if (intval($cart_item['amount']) == $amount) {
                    return 'No change in database necessary';
                } else {
                    $this->Database->update_record('cart_items', $cart_item['id'], array('amount' => $final_amt));
                    return 'Successfully increased quantity in cart.';
                }
            }
            return 'Unable to add to cart.';
        }
        /* Returns the amount of items in the user's cart */
        public function count_cart() {
            $user = $this->session->userdata('user');
            $cart_count = 0;
            if (!empty($user)) {
                $cart_count = $this->Database->count_records('cart_items', 'user_id', $user['id']);
            }
            return $cart_count;
        }
        /* Get parameters to pass to the cart view file */
        public function get_cart_param() {
            $data = $this->Database->get_cart_records();
            $cart_totals = $this->get_cart_totals($data);

            $param = $this->General->get_base_param();
            return array_merge($param, array(
                'data' => $data,
                'cart_count' => $this->count_cart(),
                'cart_total' => $cart_totals['cart_total'],
                'grand_total' => $cart_totals['grand_total'],
                'shipping_fee' => $this->Database->shipping_fee,
                'stripe_key' => $this->config->item('stripe_key'),
                'errors' => array('ship' => array(), 'bill' => array()),
                'form' => array('ship' => array(), 'bill' => array())
            ));
        }
        /* Obtain the total cost of items in the cart. cart_total is for all items, grand_total is for all items
            and the shipping fee */
        public function get_cart_totals($data) {
            $cart_totals = array('cart_total' => 0, 'grand_total' => 0);
            foreach ($data as $row) {
                $cart_totals['cart_total'] += intval(str_replace('.', '', $row['total']));
            }
            $cart_totals['grand_total'] = $cart_totals['cart_total'] + intval(str_replace('.', '', $this->Database->shipping_fee));
            foreach ($cart_totals as $key => $val) {
                /* Ensures a decimal value without resorting to using floatpoint values for computation */
                $decimal = substr($val, -2);
                if (strlen($decimal) < 2) {
                    $decimal .= '0';
                }
                $cart_totals[$key] = substr($val, 0, -2) . '.' . $decimal;
            }
            return $cart_totals;
        }
        /* Delete cart items */
        public function delete_cart_item($id) {
            /* Check if cart item exists */
            $cart_item = $this->Database->get_record('cart_items', 'id', $id);
            if ($cart_item === NULL) {
                return 'Item in cart does not exist.';
            }
            $this->Database->delete_record('cart_items', $cart_item['id']);
        }
    }
?>