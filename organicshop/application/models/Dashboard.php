<?php
    class Dashboard extends CI_Model {
        public function get_param() {
            $this->load->model('Defence');
            $this->load->model('Database');
            $prod_type = $this->Database->get_records('product_types');
            $prod_count = array('All Products' => $this->Database->count_records('products'));
            foreach ($prod_type as $row) {
                $prod_count[$row['name']] = $this->Database->count_records('products','product_type_id', $row['id']);
            }
            return array(
                'user' => $this->session->userdata('user'),
                'data' => $this->session->flashdata('data'),
                'errors' => $this->session->flashdata('errors'),
                'csrf' => $this->Defence->get_csrf(),
                'prod_count' => $prod_count
            );
        }
        public function validate($post) {
            $this->load->library('form_validation');
            $do = $this->form_validation;
            $do->set_data($post);
            $do->set_error_delimiters('<p class="errors">', '</p>');
            $rules = 'trim|required';
            $do->set_rules('product_name', 'product name', $rules);
            $do->set_rules('description', 'description', $rules);
            $do->set_rules('category', 'category', array(
                'trim', 'required', array('check_category', array($this, 'check_category'))
            ), array('check_category' => 'Invalid category detected'));
            $do->set_rules('price', 'price', array(
                'trim', 'required', 'numeric', array('check_price', array($this, 'check_price'))
            ), array('check_price' => 'Invalid price value detected'));
            $do->set_rules('stock', 'inventory', $rules . '|is_natural');
            
            $errors = array();
            if ($do->run() === FALSE) {
                foreach (array('product_name', 'description', 'category', 'price', 'stock') as $field) {
                    $errors[$field] = form_error($field);
                }
            }
            $this->session->set_flashdata('errors', $errors);
            return $errors;

            // if (count($_FILES) > 0 && empty($errors)) {
            //     $config['upload_path'] = '././assets/images/products';
            //     $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
            //     $this->load->library('upload', $config);
            //     $files = $_FILES;
            //     for($i = 0; $i < count($files['images']); $i++) {
            //         $_FILES['images']['name'] = $files['images']['name'][$i];
            //         $_FILES['images']['type'] = $files['images']['type'][$i];
            //         $_FILES['images']['tmp_name'] = $files['images']['tmp_name'][$i];
            //         $_FILES['images']['error'] = $files['images']['error'][$i];
            //         $_FILES['images']['size'] = $files['images']['size'][$i];    
            //         if (!$this->upload->do_upload('images')) {
            //             $error = array('error' => $this->upload->display_errors('<p class="errors">', '</p>'));
            //             var_dump($error);
            //         } else {
            //             $data = array('upload_data' => $this->upload->data());
        
            //             var_dump($data);
            //         }
            //     }
            
            // }
        }
        public function check_category($str) {
            $this->load->model('Database');
            $types = $this->Database->get_records('product_types');
            $type_names = array();
            foreach ($types as $row) {
                array_push($type_names, $row['name']);
            }
            if (in_array($str, $type_names)) {
                return TRUE;
            }
            return FALSE;
        }
        public function check_price($str) {
            if (floatval($str) > 0) {
                return TRUE;
            }
            return FALSE;
        }
    }
?>