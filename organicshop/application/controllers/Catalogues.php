<?php
    class Catalogues extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('Catalogue');
            $this->load->model('General');
        }
        public function index() {
            $param = $this->Catalogue->get_param();
            $this->load->view('catalogues/catalogue', $param);
        }
        public function filter() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            if (!array_key_exists('product_type', $post)) {
                $post['product_type'] = 0;
            }
            $data = $this->Catalogue->get_products($post['product_type']);
            $data = $this->General->search_products($post, $data);
            // redirect('/');
            $this->load->view('partials/catalogues/catalogue_products', array('data' => $data, 'csrf' => $this->General->get_csrf()));
        }
        public function search() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            $data = $this->General->search_products($post, $this->Catalogue->get_products());
            $this->load->view('partials/catalogues/catalogue_products', array('data' => $data, 'csrf' => $this->General->get_csrf()));
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
    }
?>