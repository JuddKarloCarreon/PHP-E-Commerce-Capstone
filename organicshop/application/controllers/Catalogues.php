<?php
    class Catalogues extends CI_Controller {
        /* Loads the models */
        public function __construct() {
            parent::__construct();
            $this->load->model('Catalogue');
            $this->load->model('General');
        }
        /* Default page */
        public function index($search = '') {
            $search = $this->General->clean($search);
            $param = $this->Catalogue->get_param();
            if ($search != '') {
                $param['data'] = $this->Catalogue->get_products(0, 0, 1, '', $search);
                $param['search_val'] = $search;
            }
            $this->load->view('catalogues/catalogue', $param);
        }
        /* Handles the filtering from search and category switching */
        public function filter() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            echo json_encode($this->General->filter($post, 'catalogue'));
        }
        /* Handles the view file for the individual product */
        public function product_view($id) {
            $this->load->model('Database');
            $id = $this->General->clean($id);
            $id = $this->Database->validate_id($id);
            $record = $this->Database->get_record('products', 'id', $id);
            if ($id !== FALSE && $record !== NULL) {
                $this->load->view('catalogues/product_view', $this->Catalogue->get_product_param($id));
            } else {
                redirect('catalogues');
            }
        }
        /* Handles the search when searching from the cart page */
        public function search() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            redirect('catalogues/index/' . $post['search']);
        }
        /* This is the function for when the add to card button is clicked */
        public function add_cart() {
            $post = $this->General->clean();
            if (!empty($post)) {
                $res = $this->Catalogue->add_cart($post);
                echo json_encode($res);
            }
        }
        /* This handles the cart page viewing */
        public function view_cart() {
            if (empty($this->session->userdata('user'))) {
                redirect('login');
            }
            $this->load->model('Database');
            if (empty($this->Database->get_records('cart_items', 'user_id', $this->session->userdata('user')['id']))) {
                redirect('catalogues');
            }
            $param = $this->Catalogue->get_cart_param();
            $this->load->view('catalogues/cart', $param);
        }
        /* This handles any modifications in the cart aside from deletion */
        public function modify_cart() {
            $post = $this->General->clean();
            $result = $this->Catalogue->add_cart($post, 'replace');
            $this->view_cart_ajax();
        }
        /* Handles removal of cart item */
        public function delete_cart_item($id) {
            $id = $this->General->clean($id);
            $id = $this->Database->validate_id($id);
            if ($id !== FALSE && $id > 0) {
                $this->Catalogue->delete_cart_item($id);
            }
            $this->view_cart_ajax();
        }
        /* Returns the view file and other data for ajax purposes */
        private function view_cart_ajax() {
            $param = $this->Catalogue->get_cart_param();
            echo json_encode(array(
                'view' => $this->load->view('partials/catalogues/cart_items', $param, TRUE),
                'cart_total' => $param['cart_total'],
                'grand_total' => $param['grand_total'],
                'cart_count' => $param['cart_count']
            ));
        }
    }
?>