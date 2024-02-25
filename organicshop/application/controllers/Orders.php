<?php
    class Orders extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Dashboard');
            $this->load->model('Order');
        }
        public function index() {
            if ($this->Dashboard->check_not_admin()) {
                redirect('catalogues');
            } else {
                $param = $this->Order->get_order_param();
                $this->load->view('dashboards/admin_orders', $param);
            }
        }
        public function set_status() {
            $post = $this->General->clean();
            if (!empty($post)) {
                $change = $this->Order->set_status($post);
                echo json_encode(array_values($this->Order->get_order_counts($this->Database->get_order_records(array(), 1, 'all', $post['search']))));
            }
        }
    }
?>