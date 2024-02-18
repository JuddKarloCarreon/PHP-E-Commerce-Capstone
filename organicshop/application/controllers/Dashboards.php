<?php
    class Dashboards extends CI_Controller {
        public function index() {
            if ($this->session->userdata('id') === NULL) {
                redirect('login');
            }
        }
    }
?>