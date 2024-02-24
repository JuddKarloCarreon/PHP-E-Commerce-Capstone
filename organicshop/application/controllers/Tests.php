<?php
    class Tests extends CI_Controller {
        public function index() {
            $this->load->library('form_validation');
            $do = $this->form_validation;
            $do->set_data(array('num'=> '0.2345'));
            $do->set_rules('num', 'num', 'trim|required|integer');
            if ($do->run() === FALSE) {
                var_dump(form_error('num'));
            } else {
                var_dump('succ');
            }
        }
    }
?>