<?php
    class Catalogue extends CI_Model {
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Database');
        }
        public function get_param() {
            $prod_count = array('All Products' => array($this->Database->count_records('products'), 0));
            foreach ($this->Database->product_types as $key => $val) {
                $prod_count[$val] = array($this->Database->count_records('products','product_type', $key + 1), $key + 1);
                $prod_type[$val] = $key + 1;
            }
            $this->session->flashdata('data'); /* To unset data from the admin_products if used */
            $data = $this->session->flashdata('cat_data');
            if ($data === NULL) {
                $data = $this->get_products();
            }
            return array(
                'user' => $this->session->userdata('user'),
                'csrf' => $this->General->get_csrf(),
                'prod_count' => $prod_count,
                'data' => $data
            );
        }
        public function get_product_param($id) {
            $main_data = $this->get_product($id);
            $data = $this->get_products($main_data['product_type'], $main_data['id']);
            return array(
                'user' => $this->session->userdata('user'),
                'csrf' => $this->General->get_csrf(),
                'main_data' => $main_data,
                'data' => $data
            );
        }
        public function get_products($type = 0, $not_id = 0) {
            if ($type != 0) {
                $data = $this->Database->get_records('products', 'product_type', $type, 'id', $not_id);
            } else {
                $data = $this->Database->get_records('products');
            }
            foreach ($data as $key => $row) {
                if (!in_array($row['image_names_json'], array('null', '[]', NULL, '[null]'))) {
                    $data[$key]['main_img'] = json_decode($row['image_names_json'])[0];
                } else {
                    $data[$key]['main_img'] = '';
                }
                $data[$key]['category'] = $this->Database->product_types[intval($row['product_type']) - 1];
                $data[$key]['full'] = intval($row['rating'][0]);
                $data[$key]['half'] = 0;
                if (intval($row['rating'][2]) >= 5) {
                    $data[$key]['half'] = 1;
                }
                $data[$key]['empty'] = 5 - ($data[$key]['full'] + $data[$key]['half']);
                $data[$key]['rating_count'] = $this->Database->count_records('reviews', 'product_id', $row['id']);
            }
            $this->session->set_flashdata('data', $data);
            return $data;
        }
        public function search($post, $data = 'none') {
            if ($data === 'none') {
                $data = $this->get_products();
            }
            return $this->General->search_products($post, $data);
        }
        public function get_product($id) {
            $data = $this->Database->get_record('products', 'id', $id);
            $data['images'] = json_decode($data['image_names_json']);
            $data['full'] = intval($data['rating'][0]);
                $data['half'] = 0;
                if (intval($data['rating'][2]) >= 5) {
                    $data['half'] = 1;
                }
                $data['empty'] = 5 - ($data['full'] + $data['half']);
                $data['rating_count'] = $this->Database->count_records('reviews', 'product_id', $data['id']);
            return $data;
        }
    }
?>