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
            $errors = $this->Dashboard->validate($post);
            redirect('/');
            // if (count($_FILES) > 0 && empty($errors)) {
            //     $config['upload_path'] = '././assets/images/products';
            //     $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
            //     $this->load->library('upload', $config);
            //     $files = $_FILES;
            //     $data = array();
            //     $errors = array();
            //     for($i = 0; $i < count($files['images']['name']); $i++) {
            //         $_FILES['images']['name'] = $files['images']['name'][$i];
            //         $_FILES['images']['type'] = $files['images']['type'][$i];
            //         $_FILES['images']['tmp_name'] = $files['images']['tmp_name'][$i];
            //         $_FILES['images']['error'] = $files['images']['error'][$i];
            //         $_FILES['images']['size'] = $files['images']['size'][$i];    
            //         if (!$this->upload->do_upload('images')) {
            //             $errors = array($this->upload->display_errors('<p class="errors">', '</p>'));
            //             break;
            //         } else {
            //             $data[$i] = $this->upload->data();
            //         }
            //     }
            //     if (!empty($errors)) {
            //         foreach ($data as $row) {
            //             unlink($row['full_path']);
            //         }
            //         $data = array();
            //     }
            //     var_dump($data);
            //     var_dump($errors);
            // }
        }
    }
?>