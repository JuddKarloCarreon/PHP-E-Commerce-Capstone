<?php
    class Generals extends CI_Controller {
        public function get_csrf() {
            $this->load->model('General');
            echo json_encode($this->General->get_csrf());
        }
    }
?>