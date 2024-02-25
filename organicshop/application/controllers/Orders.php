<?php
    class Orders extends CI_Controller {
        /* Loads models */
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Dashboard');
            $this->load->model('Order');
        }
        /* Default page */
        public function index() {
            if ($this->Dashboard->check_not_admin()) {
                redirect('catalogues');
            } else {
                $param = $this->Order->get_order_param();
                $this->load->view('dashboards/admin_orders', $param);
            }
        }
        /* Handles the setting of a new status */
        public function set_status() {
            $post = $this->General->clean();
            if (!empty($post)) {
                $change = $this->Order->set_status($post);
                echo json_encode(array_values($this->Order->get_order_counts($this->Database->get_order_records(array(), 1, 'all', $post['search']))));
            }
        }
    }
?>