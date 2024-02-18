<?php
    class Defence extends CI_Model {
        public function __construct() {
            parent::__construct();
            $this->load->helper('security');
        }
        public function clean($data = 'post') {
            if ($data === 'post') {
                return $this->input->post(NULL, TRUE);
            }
            return $this->security->xss_clean($data);
        }
        public function get_csrf() {
            return array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
        }
    }
?>