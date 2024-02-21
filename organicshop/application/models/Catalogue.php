<?php
    class Catalogue extends CI_Model {
        public function get_param() {
            $this->load->model('Defence');
            return array(
                'user' => $this->session->userdata('user'),
                'csrf' => $this->Defence->get_csrf()
            );
        }
    }
?>