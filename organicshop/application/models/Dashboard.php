<?php
    class Dashboard extends CI_Model {
        public function get_param() {
            $this->load->model('Defence');
            $this->load->model('Database');
            $table = $this->Database->get_records('product_types');
            $prod_count = array('All Products' => array($this->Database->count_records('products'), 0));
            foreach ($table as $row) {
                $prod_count[$row['name']] = array($this->Database->count_records('products','product_type_id', $row['id']), $row['id']);
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
            if ((count($_FILES['images']['name']) > 1 || $_FILES['images']['name'][0] !== '') && empty($errors)) {
                /* This part creates or clears the temporary upload directory */
                $dir = FCPATH . 'assets/images/products/temp/' . $this->session->userdata('user')['id'];
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                } else {
                    $this->delete_files($dir);
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
                        array_push($data['imgs'], $temp['file_name']);
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
            list($post, $upload_imgs) = $this->prep_for_db($post);
            var_dump($post);
            var_dump($upload_imgs);
            /* Add to database */
            // $this->Database->add_record('products', $post);
            // $id = $this->Database->get_record('products', 'product_type_id', $post['product_type_id'], 'ORDER BY created_at DESC')['id'];
            
            /* Move images to proper directory */
            if (array_key_exists('validated_imgs', $post)) {
                $this->move_product_imgs($upload_imgs, $id);
            }
        }
        public function edit_product($post) {
            $this->load->model('Database');
            $id = $post['id'];

            list($data, $upload_imgs) = $this->prep_for_db($post);
            /* Edit record */
            $this->Database->update_record('products', $id, $data);
            /* Move images to proper directory */
            if (!empty($post['validated_imgs']['imgs'])) {
                $this->move_product_imgs($upload_imgs, $id);
            }
        }
        private function prep_for_db($post) {
            $this->load->model('Database');
            /* Preparing $post for addition/update to database */
            $temp = array(
                'name' => 'product_name',
                'product_type_id' => 'category'
            );
            foreach ($temp as $key => $val) {
                $post[$key] = $post[$val];
                unset($post[$val]);
            }

            /* Prepare images */
            $images = array();
            if (array_key_exists('id', $post)) {
                /* Get images from database */
                $temp = $this->Database->get_record('products', 'id', $post['id']);
                if (!in_array($temp['image_names_json'], array('null', '[]', NULL, '[null]'))) {
                    $images = json_decode($temp['image_names_json']);
                }
            }
            $upload_imgs = array();
            if (!empty($post['validated_imgs'])) {
                $images = array_merge($images, $post['validated_imgs']['imgs']);
                $upload_imgs = $post['validated_imgs']['imgs'];
            }
            /* Put main image to the beginning of the array, then encode */
            if (array_key_exists('main_image', $post)) {
                $images = array_diff($images, array($post['main_image']));
                $images = array_merge(array($post['main_image']), $images);
            }
            $post['image_names_json'] = json_encode($images);
            /* Remove excess key value pairs from post */
            $temp = array('main_image', 'validated_imgs', 'id');
            foreach ($temp as $val) {
                unset($post[$val]);
            }
            return array($post, $upload_imgs);
        }
        private function move_product_imgs($imgs, $id) {
            $path = str_replace('\\', '/', FCPATH . 'assets/images/products/');
            $new = $path . $id . '/';
            if (!is_dir($new)) {
                mkdir($new, 0777, true);
            }
            foreach ($imgs as $img) {
                rename($path . 'temp/' . $this->session->userdata('user')['id'] . '/' . $img, $new . $img);
            }
        }
        public function get_products($type = 0) {
            $this->load->model('Database');
            $type = $this->Database->validate_id($type);
            $data = array();
            if ($type !== FALSE) {
                $where = '';
                if ($type != 0) {
                    $where = "WHERE t1.product_type_id='$type'";
                }

                $query = "SELECT t1.*, t2.name as category FROM products t1 LEFT JOIN product_types t2 ON t1.product_type_id=t2.id $where";
                $data = $this->db->query($query)->result_array();

                foreach ($data as $key => $row) {
                    $data[$key]['main_img'] = '';
                    if (!in_array($row['image_names_json'], array('null', '[]', NULL, '[null]'))) {
                        $data[$key]['main_img'] = json_decode($row['image_names_json'])[0];
                    } else {
                        $data[$key]['image_names_json'] = '';
                    }
                }
            }
            $this->session->set_flashdata('data', $data);
            return $data;
        }
        public function delete_image($id, $name) {
            $this->load->model('Database');
            $images = $this->Database->get_record('products', 'id', $id)['image_names_json'];
            $images = json_decode($images);
            $images = array_diff($images, array($name));
            $images = array_merge(array(), $images);
            $images = array('image_names_json' => json_encode($images));
            $this->Database->update_record('products', $id, $images);

            $file = str_replace('\\', '/', FCPATH . "assets/images/products/$id/$name");
            unlink($file);
        }
        public function delete_record($table, $id) {
            $dir = str_replace('\\', '/', FCPATH . "assets/images/products/$id");
            $this->delete_files($dir);
            $this->Database->delete_record($table, $id);
        }
        private function delete_files($dir) {
            $files = glob($dir . '/*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file);
                }
            }
        }
        public function search($post, $data = 'none') {
            if ($data === 'none') {
                $data = $this->get_products();
            }
            if ($post['search'] == '') {
                return $data;
            }
            $results = array();
            foreach ($data as $row) {
                if (strpos(strtolower($row['name']), strtolower($post['search'])) !== FALSE) {
                    array_push($results, $row);
                }
            }
            return $results;
        }
        public function check_not_admin() {
            $user = $this->session->userdata('user');
            if ($user === NULL || $user['is_admin'] == 0) {
                return TRUE;
            }
            return FALSE;
        }
    }
?>