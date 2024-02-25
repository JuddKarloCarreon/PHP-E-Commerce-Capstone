<?php
    class General extends CI_Model {
        /* Loads the security helper for all methods in this model */
        public function __construct() {
            parent::__construct();
            $this->load->helper('security');
            
        }
        /* Cleans data. If no data passed, returns cleaned post data */
        public function clean($data = 'post') {
            if ($data === 'post') {
                return $this->input->post(NULL, TRUE);
            }
            return $this->security->xss_clean($data);
        }
        /* Returns necassary csrf security credentials */
        public function get_csrf() {
            return array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
        }
        public function get_base_param() {
            return array(
                'user' => $this->session->userdata('user'),
                'csrf' => $this->get_csrf()
            );
        }
        public function get_page_param($current = 1, $type = 0, $search = '', $kind = 'products') {
            $this->load->model('Database');
            $page = array();
            $count = $this->Database->count_records_search($type, $search, $kind);
            $page['count'] = ceil($count / $this->Database->item_limit);
            if ($page['count'] == 0) {
                $page['count'] = 1;
            }
            $page['current'] = 1;
            if (intval($current) > 0 && intval($current) <= $page['count']) {
                $page['current'] = intval($current);
            }
            return $page;
        }
        public function filter($post, $page_type, $ret = 0) {
            $check = array('product_type' => 0, 'page' => 1, 'status' => 0);
            foreach ($check as $key => $val) {
                if (!array_key_exists($key, $post)) {
                    $post[$key] = $val;
                }
            }
            $csrf = $this->get_csrf();
            if ($page_type == 'catalogue') {
                $this->load->model('Catalogue');
                $data = $this->Catalogue->get_products($post['product_type'], 0, $post['page'], '', $post['search']);
                $data_view = $this->load->view('partials/catalogues/catalogue_products', array('data' => $data, 'csrf' => $csrf, 'hide_pages' => NULL), TRUE);
            } else if ($page_type == 'dashboard') {
                $this->load->model('Dashboard');
                $data = $this->Dashboard->get_products($post['product_type'], $post['page'], '', $post['search']);
                $data_view = $this->load->view('partials/dashboards/admin_products_data', array('data' => $data, 'csrf' => $csrf), TRUE);
            } else if ($page_type == 'order') {
                $this->load->model('Order');
                $data = $this->Database->get_order_records(array('status' => $post['status']), $post['page'], '', $post['search']);
                $data_view = $this->load->view('partials/dashboards/admin_orders_data', array('data' => $data, 'csrf' => $csrf, 'status_types' => $this->Database->order_status), TRUE);
            }

            if ($page_type != 'order') {
                $kind = 'products';
                $type = $post['product_type'];
                $prod_count = array('All Products' => array($this->Database->count_records_search(0, $post['search']), 0));
                foreach ($this->Database->product_types as $key => $val) {
                    $prod_count[$val] = array($this->Database->count_records_search($key + 1, $post['search']), $key + 1);
                }
            } else {
                $kind = 'orders';
                $type = $post['status'];
                $prod_count = $this->Order->get_order_counts($this->Database->get_order_records(array('status' => $post['status']), $post['page'], 'all', $post['search']));
            }
            $page = $this->get_page_param($post['page'], $type, $post['search'], $kind);
            // redirect('/');
            if ($ret == 0) {
                return array(
                    'data' => $data_view,
                    'page' => $this->load->view('partials/global/page_select', array('page' => $page, 'csrf' => $csrf), TRUE),
                    'categories' => $this->load->view('partials/global/categories_form', array('prod_count' => $prod_count), TRUE)
                );
            }
            return array(
                'data' => $data,
                'page' => $page,
                'csrf' => $csrf,
                'prod_count' => $prod_count
            );
        }
    }
?>