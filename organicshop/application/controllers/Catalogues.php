<?php
    class Catalogues extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('Catalogue');
            $this->load->model('General');
        }
        public function index($search = '') {
            $search = $this->General->clean($search);
            $param = $this->Catalogue->get_param();
            if ($search != '') {
                $param['data'] = $this->Catalogue->get_products(0, 0, 1, '', $search);
                $param['search_val'] = $search;
            }
            $this->load->view('catalogues/catalogue', $param);
        }
        public function filter() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            echo json_encode($this->General->filter($post, 'catalogue'));
        }
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
        public function search() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            redirect('catalogues/index/' . $post['search']);
        }
        public function add_cart() {
            $post = $this->General->clean();
            if (!empty($post)) {
                $res = $this->Catalogue->add_cart($post);
                echo json_encode($res);
            }
        }
    }
?>