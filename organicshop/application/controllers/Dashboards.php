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
            $user = $this->session->userdata('user');
            if ($user === NULL || $user['is_admin'] == 0) {
                $this->load->view('dashboards/catalogue', $this->Dashboard->get_param());
            } else {
                $this->load->view('dashboards/admin_products', $this->Dashboard->get_param());
            }
        }
        public function add_product() {
            $post = $this->Defence->clean();
            list($errors, $post) = $this->Dashboard->validate($post);
            if (empty($errors)) {
                $this->Dashboard->add_product($post);
            }
            redirect('/');
        }
        public function edit_product() {
            $post = $this->Defence->clean();
            var_dump($post);
        }
        public function get_record($id) {
            $id = $this->Defence->clean($id);
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if ($id) {
                $this->load->model('Database');
                echo json_encode($this->Database->get_record('products', 'id', $id));
            }
        }
        public function test() {
            var_dump($this->session->userdata());
            $dir = '././assets/images/products/temp/' . $this->session->userdata('user')['id'];
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            } else {
                $files = glob($dir . '/*');
                var_dump($files);
                foreach($files as $file){
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }
?>