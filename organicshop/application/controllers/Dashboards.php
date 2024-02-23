<?php
    class Dashboards extends CI_Controller {
        /* Loads the Dashboard model for all methods here */
        public function __construct() {
            parent::__construct();
            $this->load->model('Dashboard');
            $this->load->model('General');
        }
        /* The default page of the website */
        public function index() {
            $param = $this->Dashboard->get_param();
            if ($this->Dashboard->check_not_admin()) {
                redirect('catalogues');
            } else {
                $this->load->view('dashboards/admin_products', $param);
            }
        }
        public function add_product() {
            $post = $this->General->clean();
            if ($this->Dashboard->check_not_admin() || empty($post)) {
                redirect('/');
            }
            list($errors, $post) = $this->Dashboard->validate($post);
            if (empty($errors)) {
                $this->Dashboard->add_product($post);
            }
            redirect('/');
        }
        public function edit_product() {
            $post = $this->General->clean();
            if ($this->Dashboard->check_not_admin() || empty($post)) {
                redirect('/');
            }
            list($errors, $post) = $this->Dashboard->validate($post);
            if (empty($errors)) {
                $this->Dashboard->edit_product($post);
            }
            redirect('/');
        }
        public function delete_image($id, $name) {
            if ($this->Dashboard->check_not_admin()) {
                redirect('/');
            }
            $this->load->model('Database');
            $name = $this->General->clean($name);
            $id = $this->Database->validate_id($id);
            if ($id) {
                $this->Dashboard->delete_image($id, $name);
            }
        }
        public function get_record($id) {
            if ($this->Dashboard->check_not_admin()) {
                redirect('/');
            }
            $this->load->model('Database');
            $id = $this->Database->validate_id($id);
            if ($id) {
                echo json_encode($this->Database->get_record('products', 'id', $id));
            }
        }
        public function delete_product() {
            if ($this->Dashboard->check_not_admin()) {
                redirect('/');
            }
            $this->load->model('Database');
            $id = $this->input->post('id', TRUE);
            $id = $this->Database->validate_id($id);
            if ($id) {
                $this->Database->soft_delete_record('products', $id);
                // $this->Dashboard->soft_delete_record('products', $id);
            }
            redirect('/');
        }
        public function filter() {
            $post = $this->General->clean();
            if ($this->Dashboard->check_not_admin() || empty($post)) {
                redirect('/');
            }
            // redirect('/');
            echo json_encode($this->General->filter($post, 'dashboard'));
        }
    }
?>