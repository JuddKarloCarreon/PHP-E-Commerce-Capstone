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
            if ($this->Dashboard->check_not_admin()) {
                redirect('catalogues');
            } else {
                $param = $this->Dashboard->get_param();
                $this->load->view('dashboards/admin_products', $param);
            }
        }
        /* Handles the request to add a new product */
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
        /* Handes the request to edit a product */
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
        /* Handles image deletion from the product */
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
        /* Handles obtaining the data view file for a specific product when editing */
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
        /* Handles product deletion by setting the active column to 0 */
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
        /* Handles the filtering of data from the search and switching of columns */
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