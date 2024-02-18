<?php
    class User extends CI_Model {
        public function validate($post) {
            $this->load->library('form_validation');
            $errors = array();
            $do = $this->form_validation;
            $do->set_data($post);
            $do->set_error_delimiters('<p class="errors">', '</p>');
            if ($post['action'] === 'login') {
                $do->set_rules('email', 'email', array(
                    'trim', 'required', 'valid_email',
                    function ($email) {
                        return $this->find_email($email);
                    })
                );
                $this->check = $post['email'];
                $do->set_rules('password', 'password', array(
                    'trim',
                    function ($password) {
                        return $this->check_password($this->check, $password);
                    }
                ));
            } else {
                $rules = 'trim|required';
                $do->set_rules('first_name', 'first name', $rules);
                $do->set_rules('last_name', 'last name', $rules);
                $do->set_rules('email', 'email', $rules . '|valid_email|is_unique[users.email]');
                $do->set_rules('password', 'password', $rules .'|min_length[8]');
                $do->set_rules('confirm_password', 'confirm password', $rules .'|matches[password]');
            }
            if ($do->run() === FALSE) {
                if ($post['action'] === 'login') {
                    $errors = array('<p class="errors">Invalid Credentials</p>');
                    
                } else {
                    foreach (array_keys($post) as $key) {
                        if ($key != 'action') {
                            $errors[$key] = form_error($key);
                        }
                    }
                }
            }
            unset($this->check);
            $this->session->set_flashdata('errors', $errors);
            return $errors;
        }
        private function find_email($email) {
            $this->load->model('Database');
            if ($this->Database->get_record('users', 'email', $email) === NULL) {
                return FALSE;
            }
            return TRUE;
        }
        private function check_password($email, $password) {
            $this->load->model('Database');
            $record = $this->Database->get_record('users', 'email', $email);
            if ($record === NULL || $record['password'] !== md5($password . '' . $record['salt'])) {
                return FALSE;
            }
            return TRUE;
        }
        public function get_param() {
            $this->load->model('Defence');
            return array(
                'csrf' => $this->Defence->get_csrf(),
                'errors' => $this->session->flashdata('errors')
            );
        }
        public function prepare_add($post) {
            $fields = array('action', 'confirm_password');
            foreach ($fields as $key) {
                unset($post[$key]);
            }
            $salt = bin2hex(openssl_random_pseudo_bytes(22));
            $post['password'] = md5($post['password'] . '' . $salt);
            $post['salt'] = $salt;
            return $post;
        }
    }
?>