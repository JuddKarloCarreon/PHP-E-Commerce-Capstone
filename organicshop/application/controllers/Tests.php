<?php
    class Tests extends CI_Controller {
        public function index() {
            $this->load->model('Database');
            var_dump($this->Database->get_order_records(array('status' => 0), 2, '', ''));
        }
    }
?>