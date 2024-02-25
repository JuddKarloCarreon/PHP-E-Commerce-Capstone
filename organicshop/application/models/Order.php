<?php
    class Order extends CI_Model {
        /* Loads models for all functions */
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Database');
        }
        /* Returns necessary parameters for the intended view file */
        public function get_order_param($status = 'all') {
            /* Check if status is valid */
            $temp = array();
            if ($status != 'all') {
                $status = $this->Database->validate_id($status);
                if ($status !== FALSE) {
                    $temp = array('status' => $status);
                }
            }
            $status = $temp;

            $data = $this->Database->get_order_records($status);
            $param = $this->General->get_base_param();
            return array_merge($param, array(
                'data' => $data,
                'status_types' => $this->Database->order_status,
                'prod_count' => $this->get_order_counts($this->Database->get_order_records($status, 1, 'all')),
                'page' => $this->General->get_page_param(1, 0, '', 'orders'),
                'hide_pages' => NULL,
                'search_val' => ''
            ));
        }
        /* Returns all the count values of for the admin orders page categories/status */
        public function get_order_counts($data) {
            $statuses = $this->Database->order_status;
            $result = array('All Orders' => array(count($data), 0));
            foreach ($statuses as $key => $val) {
                $result[$val] = array(0, $key + 1);
            }
            foreach ($data as $key => $row) {
                $result[$statuses[intval($row['status']) - 1]][0]++;
            }
            return $result;
        }
        /* Updates the status in the database */
        public function set_status($post) {
            $status = intval($post['status']);
            $order_status = array_keys($this->Database->order_status);
            if ($status < 1 || $status > (end($order_status) + 1)){
                return 'Invalid status';
            }
            $record = $this->Database->get_record('orders', 'id', $post['id']);
            if ($record === NULL) {
                return 'No record found';
            }
            if ($record['status'] != $status) {
                $this->Database->update_record('orders', $record['id'], array('status' => $status));
            }
            return 'Done';
        }
    }
?>