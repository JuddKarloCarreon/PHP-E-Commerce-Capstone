<?php
    class Dashboards extends CI_Controller {
        /* Loads the Dashboard model for all methods here */
        public function __construct() {
            parent::__construct();
            $this->load->model('Dashboard');
            $this->load->model('Defence');
        }
        /* The default page of the website */
        public function index() {
            $param = $this->Dashboard->get_param();
            if ($this->Dashboard->check_not_admin()) {
                $this->load->view('dashboards/catalogue', $param);
            } else {
                $this->load->view('dashboards/admin_products', $param);
            }
        }
        public function add_product() {
            $post = $this->Defence->clean();
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
            $post = $this->Defence->clean();
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
            $name = $this->Defence->clean($name);
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
                $this->Dashboard->delete_record('products', $id);
            }
            redirect('/');
        }
        public function change_category() {
            $post = $this->Defence->clean();
            if ($this->Dashboard->check_not_admin() || empty($post)) {
                redirect('/');
            }
            $data = $this->Dashboard->get_products($post['product_type']);
            $data = $this->Dashboard->search($post, $data);
            // redirect('/');
            $this->load->view('partials/dashboards/admin_products_data', array('data' => $data, 'csrf' => $this->Defence->get_csrf()));
        }
        public function get_csrf() {
            echo json_encode($this->Defence->get_csrf());
        }
        public function search() {
            if ($this->Dashboard->check_not_admin()) {
                redirect('/');
            }
            $post = $this->Defence->clean();
            $data = $this->Dashboard->search($post);
            $this->load->view('partials/dashboards/admin_products_data', array('data' => $data, 'csrf' => $this->Defence->get_csrf()));
        }
    }
?>