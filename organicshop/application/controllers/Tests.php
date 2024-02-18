<?php
    class Tests extends CI_Controller {
        public function index() {
            $this->load->model('Database');
            if (($record = $this->Database->get_record('users', 'email', 'aaa')) === NULL) {
                var_dump('aaa');
            } else {
                var_dump('bbb');
            }
        }
    }
?>