<?php
    class Users extends CI_Controller {
        public function login() {
            $this->base_view('users/login');
        }
        public function signup() {
            $this->base_view('users/signup');
        }
        private function base_view($view) {
            if ($this->session->userdata('id') !== NULL) {
                redirect('/');
            }
            $this->load->model('User');
            $param = $this->User->get_param();
            $this->load->view($view, $param);
        }
        public function logout() {
            $this->session->sess_destroy();
            redirect('login');
        }
        public function process_post() {
            $this->load->model('Defence');
            $post = $this->Defence->clean();
            if (empty($post)) {
                redirect('/');
            }
            $this->load->model('User');
            $errors = $this->User->validate($post);
            if (empty($errors)) {
                if ($post['action'] === 'signup') {
                    $this->load->model('Database');
                    $data = $this->User->prepare_add($post);
                    $this->Database->add_record('users', $data);
                }
                $record = $this->Database->get_record('users', 'email', $post['email']);
                $this->session->set_userdata('id', $record['id']);
            } else {
                redirect($post['action']);
            }
            redirect('/');
        }
    }
?>