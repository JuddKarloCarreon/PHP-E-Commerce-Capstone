<?php
    class Generals extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
        }
        public function get_csrf() {
            echo json_encode($this->General->get_csrf());
        }
        public function filter($page) {
            $this->load->model('Dashboard');
            $post = $this->General->clean();
            $page = $this->General->clean($page);
            if (((!$this->Dashboard->check_not_admin() && $page == 'dashboard') || $page == 'catalogue') && !empty($post) && in_array($page, array('dashboard', 'catalogue'))) {
                echo json_encode($this->General->filter($post, $page));
            }
        }
    }
?>