<?php
    class Dashboard extends CI_Model {
        /* Obtain the parameters to pass to the admin pages */
        public function get_param() {
            $this->load->model('General');
            $this->load->model('Database');
            $prod_count = array('All Products' => array($this->Database->count_records('products'), 0));
            foreach ($this->Database->product_types as $key => $val) {
                $prod_count[$val] = array($this->Database->count_records('products','product_type', $key + 1), $key + 1);
                $prod_type[$val] = $key + 1;
            }
            $data = $this->session->flashdata('data');
            if ($data === NULL) {
                $data = $this->get_products();
            }
            $page = $this->session->flashdata('page');
            if ($page === NULL) {
                $page = $this->General->get_page_param();
            }
            $param = $this->General->get_base_param();
            return array_merge($param, array(
                'data' => $data,
                'errors' => $this->session->flashdata('errors'),
                'prod_count' => $prod_count,
                'prod_type' => $prod_type,
                'page' => $page
            ));
        }
        /* Validation for the addition of a product. Includes custom validations */
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
                /* Handles the image uploads */
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
                        array_push($data, $temp['file_name']);
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
        /* Function that checks the category of the input. This is a custom validation */
        public function check_category($str) {
            $this->load->model('Database');
            $type_ids = array_keys($this->Database->product_types);
            foreach ($type_ids as $key) {
                array_push($type_ids, $key + 1);
            }
            if (in_array($str, $type_ids)) {
                return TRUE;
            }
            return FALSE;
        }
        /* Function that checks if the price is greater than 0. This is a custom validation */
        public function check_price($str) {
            if (floatval($str) > 0) {
                return TRUE;
            }
            return FALSE;
        }
        /* Function that handles the addition of products to the database */
        public function add_product($post) {  
            $this->load->model('Database'); 
            list($post, $upload_imgs) = $this->prep_for_db($post);
            /* Add to database */
            $this->Database->add_record('products', $post);
            $id = $this->Database->get_record('products', 'product_type', $post['product_type'], 'ORDER BY created_at DESC')['id'];
            
            /* Move images to proper directory */
            if (!empty($upload_imgs)) {
                $this->move_product_imgs($upload_imgs, $id);
            }
        }
        /* Handles the update of products to the database */
        public function edit_product($post) {
            $this->load->model('Database');
            $id = $post['id'];

            list($post, $upload_imgs) = $this->prep_for_db($post);
            /* Edit record */
            $this->Database->update_record('products', $id, $post);
            /* Move images to proper directory */
            if (!empty($upload_imgs)) {
                $this->move_product_imgs($upload_imgs, $id);
            }
        }
        /* Prepares the data formatting/arrangement for database upload */
        private function prep_for_db($post) {
            $this->load->model('Database');
            /* Preparing $post for addition/update to database */
            $temp = array(
                'name' => 'product_name',
                'product_type' => 'category'
            );
            foreach ($temp as $key => $val) {
                $post[$key] = $post[$val];
                unset($post[$val]);
            }

            /* Prepare images */
            $images = array();
            if ($post['id'] != '0') {
                /* Get images from database */
                $temp = $this->Database->get_record('products', 'id', $post['id']);
                if (!in_array($temp['image_names_json'], array('null', '[]', NULL, '[null]'))) {
                    $images = json_decode($temp['image_names_json']);
                }
            }
            /* Ensures unique filename for images */
            $upload_imgs = array();
            if (!empty($post['validated_imgs'])) {
                $path = str_replace('\\', '/', FCPATH . 'assets/images/products/temp/') . $this->session->userdata('user')['id'] . '/';
                foreach ($post['validated_imgs'] as $img) {
                    $temp = explode('.', $img);
                    $name = url_title($temp[0] . date('YmdHis')) . '.' . end($temp);
                    if ($img == $post['main_image']) {
                        $post['main_image'] = $name;
                    }
                    array_push($upload_imgs, $name);
                    rename($path . $img, $path . $name);
                }
                $images = array_merge($images, $upload_imgs);
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
        /* Moves images from the temporary location to a permanent one */
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
        /* Function to obtain the all the information about the products in the database */
        public function get_products($type = 0, $page = 1, $lim = '', $search = '') {
            $this->load->model('Database');
            $type = $this->Database->validate_id($type);
            $data = array();
            if ($type !== FALSE) {
                $field = 1;
                if ($type != 0) {
                    $field = 'product_type';
                }
                $data = $this->Database->get_records('products', $field, $type, 1, 0, $page, $lim, 'name', "%$search%");
                foreach ($data as $key => $row) {
                    $data[$key]['category'] = $this->Database->product_types[intval($row['product_type']) - 1];
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
        /* Function to delete the images from both the database and the file storage */
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
        /* Deletes all files in a directory */
        private function delete_files($dir) {
            $files = glob($dir . '/*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file);
                }
            }
        }
        /* Handles the search of data by obtaining all data and then passing it to the search products 
            function in the general model */
        public function search($post, $data = 'none') {
            if ($data === 'none') {
                $data = $this->get_products();
            }
            return $this->General->search_products($post, $data);
        }
        /* Checks if the logged in user is an admin or not */
        public function check_not_admin() {
            $user = $this->session->userdata('user');
            if ($user === NULL || $user['is_admin'] == 0) {
                return TRUE;
            }
            return FALSE;
        }
    }
?>