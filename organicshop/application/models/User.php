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
        /* Handles the posting for reviews and replies. Returns the resulting view for the reviews and the product rating */
        public function user_post($post, $type) {
            if ($post['content'] == '') {
                return array('errors' => 'Review cannot be empty');
            }
            $this->load->model('Database');
            $this->load->model('General');
            $this->load->model('Catalogue');
            $prod_id = $post['product_id'];
            if ($type == 'replies') {
                unset($post['product_id']);
            } else {
                /* Check new rating value and update product */
                $rating = intval($post['rating']);
                $sum = $this->db->query("SELECT SUM(rating) as sum FROM reviews WHERE product_id=?", array($prod_id))->row_array()['sum'];
                $count = $this->db->query("SELECT count(id) as count FROM reviews WHERE product_id=?", array($prod_id))->row_array()['count'];
                $avg = strval(intval($sum) + $rating) / (intval($count) + 1);
                if (strlen($avg) == 1) {
                    $avg .= '.0';
                } else if (strlen($avg) == 2) {
                    $avg .= '0';
                }
                $avg = substr($avg, 0, 3);
                $this->Database->update_record('products', $prod_id, array('rating' => $avg));
            }
            $post['user_id'] = $this->session->userdata('user')['id'];
            $this->Database->add_record($type, $post);
            $reviews = $this->get_reviews($prod_id);
            $review_param = array('main_data' => array('id' => $prod_id), 'csrf' => $this->General->get_csrf(), 'reviews' => $reviews);
            return array('view' => $this->load->view('partials/catalogues/reviews', $review_param, TRUE), 'product_data' => $this->load->view('partials/catalogues/product_data', $this->Catalogue->get_product_param($prod_id), TRUE));
        }
        /* Obtains and sets the reviews, and their respective replies */
        public function get_reviews($prod_id) {
            $reviews = $this->db->query("SELECT reviews.*, CONCAT(first_name, ' ', last_name) as name, DATE_FORMAT(reviews.created_at, '%b %d %Y') as date FROM reviews LEFT JOIN users ON reviews.user_id=users.id WHERE product_id=? ORDER BY reviews.created_at DESC", array($prod_id))->result_array();
            foreach ($reviews as $key => $row) {
                $reviews[$key]['full'] = intval($row['rating'][0]);
                $reviews[$key]['empty'] = 5 - $reviews[$key]['full'];
                $reviews[$key]['replies'] = $this->db->query("SELECT replies.*, CONCAT(first_name, ' ', last_name) as name, DATE_FORMAT(replies.created_at, '%b %d %Y') as date FROM replies LEFT JOIN users ON replies.user_id=users.id WHERE review_id=? ORDER BY replies.created_at", array($row['id']))->result_array();
            }
            return $reviews;
        }
    }
?>