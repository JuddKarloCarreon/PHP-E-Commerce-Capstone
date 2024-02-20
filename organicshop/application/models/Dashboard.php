<?php
    class Dashboard extends CI_Model {
        public function get_param() {
            $this->load->model('Defence');
            $this->load->model('Database');
            $table = $this->Database->get_records('product_types');
            $prod_count = array('All Products' => $this->Database->count_records('products'));
            foreach ($table as $row) {
                $prod_count[$row['name']] = $this->Database->count_records('products','product_type_id', $row['id']);
                $prod_type[$row['name']] = $row['id'];
            }
            $data = $this->session->flashdata('data');
            if ($data === NULL) {
                $data = $this->get_products();
            }
            return array(
                'user' => $this->session->userdata('user'),
                'data' => $data,
                'errors' => $this->session->flashdata('errors'),
                'csrf' => $this->Defence->get_csrf(),
                'prod_count' => $prod_count,
                'prod_type' => $prod_type
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
            $fields = array('product_name', 'description', 'category', 'price', 'stock');
            if ($do->run() === FALSE) {
                foreach ($fields as $field) {
                    $errors[$field] = form_error($field);
                }
                $errors['images'] = '';
            }
            $data = array();
            if (count($_FILES) > 0 && empty($errors)) {
                if (!array_key_exists('main_image', $post)) {
                    $main = $_FILES['images']['name'][0];
                } else {
                    $main = $post['main_image'];
                }
                
                /* This part creates or clears the temporary upload directory */
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

                $config['upload_path'] = $dir;
                $config['allowed_types'] = 'gif|jpg|jpeg|png|svg';
                $this->load->library('upload', $config);
                $files = $_FILES;
                $errors = array();
                for($i = 0; $i < count($files['images']['name']); $i++) {
                    $_FILES['images']['name'] = $files['images']['name'][$i];
                    $_FILES['images']['type'] = $files['images']['type'][$i];
                    $_FILES['images']['tmp_name'] = $files['images']['tmp_name'][$i];
                    $_FILES['images']['error'] = $files['images']['error'][$i];
                    $_FILES['images']['size'] = $files['images']['size'][$i];

                    if (!$this->upload->do_upload('images')) {
                        $errors['images'] = $this->upload->display_errors('<p class="errors">', '</p>');
                        break;
                    } else {
                        $temp = $this->upload->data();
                        if (!array_key_exists('file_path', $data)) {
                            $data['file_path'] = $temp['file_path'];
                        }
                        if (!array_key_exists('imgs', $data)) {
                            $data['imgs'] = array();
                        }
                        if ($_FILES['images']['name'] == $main) {
                            /* Puts the main image as first in the array */
                            $data['imgs'] = array_merge(array($temp['file_name']), $data['imgs']);
                        } else {
                            array_push($data['imgs'], $temp['file_name']);
                        }
                    }
                }
                if (!empty($errors)) {
                    foreach ($fields as $field) {
                        $errors[$field] = '';
                    }
                    foreach ($data as $row) {
                        unlink($row['full_path']);
                    }
                    $data = array();
                }
            }
            $post['validated_imgs'] = $data;
            var_dump($post);
            $this->session->set_flashdata('errors', $errors);
            return array($errors, $post);
        }
        public function check_category($str) {
            $this->load->model('Database');
            $types = $this->Database->get_records('product_types');
            $type_ids = array();
            foreach ($types as $row) {
                array_push($type_ids, $row['id']);
            }
            if (in_array($str, $type_ids)) {
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
        public function add_product($post) {
            $this->load->model('Database');
            /* Preparing $post for addition to database */
            $temp = array(
                'name' => 'product_name',
                'product_type_id' => 'category'
            );
            foreach ($temp as $key => $val) {
                $post[$key] = $post[$val];
                unset($post[$val]);
            }

            $path = substr($post['validated_imgs']['file_path'], 0, -5);
            $post['image_names_json'] = json_encode($post['validated_imgs']['imgs']);

            $temp = array('main_image', 'validated_imgs');
            foreach ($temp as $val) {
                unset($post[$val]);
            }
            
            /* Add to database */
            $this->Database->add_record('products', $post);
            $id = $this->Database->get_record('products', 'product_type_id', $post['product_type_id'], 'ORDER BY created_at DESC')['id'];
            
            /* Move images to proper directory */
            $this->handle_product_imgs($path, json_decode($post['image_names_json']), $id);
        }
        private function handle_product_imgs($path, $imgs, $id) {
            $new .= $id . '/';
            if (!is_dir($new)) {
                mkdir($new, 0777, true);
            }
            foreach ($imgs as $img) {
                rename($path . 'temp/' . $img, $new . $img);
            }
        }
        private function get_products($type = 0) {
            $where = '';
            if ($type != 0) {
                $where = "t1.product_type_id='$type'";
            }

            $query = "SELECT t1.*, t2.name as category FROM products t1 LEFT JOIN product_types t2 ON t1.product_type_id=t2.id $where";
            $data = $this->db->query($query)->result_array();

            foreach ($data as $key => $row) {
                $data[$key]['main_img'] = '';
                if ($row['image_names_json'] !== 'null') {
                    $data[$key]['main_img'] = json_decode($row['image_names_json'])[0];
                }
            }
            return $data;
        }
    }
?>