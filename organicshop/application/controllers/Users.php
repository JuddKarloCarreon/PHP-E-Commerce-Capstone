<?php
    class Users extends CI_Controller {
        /* Handles user login */
        public function login() {
            $this->base_view('users/login');
        }
        /* Handles user signup */
        public function signup() {
            $this->base_view('users/signup');
        }
        /* Handles user login and signup due to both having similar codes. */
        private function base_view($view) {
            if ($this->session->userdata('user') !== NULL) {
                redirect('/');
            }
            $this->load->model('User');
            $param = $this->User->get_param();
            $this->load->view($view, $param);
        }
        /* Handles user logout, and redirects to default page */
        public function logout() {
            $this->session->sess_destroy();
            redirect('/');
        }
        /* Handles form submission for login and signup. First cleans and checks the existence post data,
            then validates it, and if doing signup, adds new user to the database, then logs in. */
        public function process_post() {
            $this->load->model('General');
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            $this->load->model('User');
            $errors = $this->User->validate($post);
            if (empty($errors)) {
                $this->User->process_user($post);
            } else {
                redirect($post['action']);
            }
            redirect('/');
        }
    }
?>