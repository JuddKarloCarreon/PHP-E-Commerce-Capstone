<?php
    class User extends CI_Model {
        /* Handles validation for login and signup. Sets detected errors to flashdata, and also returns it */
        public function validate($post) {
            $this->load->library('form_validation');
            $errors = array();
            /* This shortens the code */
            $do = $this->form_validation;
            /* Sets the data to validate */
            $do->set_data($post);
            /* Sets the prefix and suffix of the error messages */
            $do->set_error_delimiters('<p class="errors">', '</p>');
            if ($post['action'] === 'login') {
                /* Sets rules for the email field along with custom validation. Note that this
                    specific syntax was the only way I got the custom validation to work */
                $do->set_rules('email', 'email', array(
                    'trim', 'required', 'valid_email',
                    function ($email) {
                        return $this->find_email($email);
                    })
                );
                /* Sets data to be accessible within the anonymous function */
                $this->check = $post['email'];
                /* Sets rules for the password field along with custom validation. Note that this
                    specific syntax was the only way I got the custom validation to work */
                $do->set_rules('password', 'password', array(
                    'trim', 'required',
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
                /* Post data is set as flashdata in case of errors, so old form data will won't
                    disappear when the page is refreshed */
                $form = $this->unset_fields('login', $post);
                $this->session->set_flashdata('form', $form);
                if ($post['action'] === 'login') {
                    /* Creates a singular error message for login errors */
                    $errors = array('<p class="errors">Invalid Credentials</p>');
                } else {
                    /* Creates individual error messages for each field */
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
        /* Handles the custom validation for email */
        private function find_email($email) {
            $this->load->model('Database');
            if ($this->Database->get_record('users', 'email', $email) === NULL) {
                return FALSE;
            }
            return TRUE;
        }
        /* Handles the custom validation for password checking */
        private function check_password($email, $password) {
            $this->load->model('Database');
            $record = $this->Database->get_record('users', 'email', $email);
            if ($record === NULL || $record['password'] !== md5($password . '' . $record['salt'])) {
                return FALSE;
            }
            return TRUE;
        }
        /* Handles obtaining parameters to pass to the login and signup views */
        public function get_param() {
            $this->load->model('General');
            return array(
                'csrf' => $this->General->get_csrf(),
                'errors' => $this->session->flashdata('errors'),
                'form' => $this->session->flashdata('form')
            );
        }
        /* Prepares the post data to add a new user to the database */
        public function prepare_add($post) {
            $post = $this->unset_fields('signup', $post);
            $salt = bin2hex(openssl_random_pseudo_bytes(22));
            $post['password'] = md5($post['password'] . '' . $salt);
            $post['salt'] = $salt;
            return $post;
        }
        /* Unsets unwanted fields in the post data */
        private function unset_fields($form, $post) {
            $fields = array('action', 'confirm_password');
            if ($form == 'login') {
                array_push($fields, 'password');
            }
            foreach ($fields as $key) {
                unset($post[$key]);
            }
            return $post;
        }
        /* Handles the login and signup processes */
        public function process_user($post) {
            if ($post['action'] == 'signup') {
                $this->load->model('Database');
                $data = $this->User->prepare_add($post);
                $this->Database->add_record('users', $data);
            }
            
            $record = $this->Database->get_record('users', 'email', $post['email']);
            $this->session->set_userdata('user', array(
                'id' => $record['id'],
                'name' => $record['first_name'],
                'is_admin' => $record['user_level'],
                'image' => $record['image']
            ));
        }
    }
?>