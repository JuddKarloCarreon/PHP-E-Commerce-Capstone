<?php
    class Generals extends CI_Controller {
        /* Loads the model */
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
        }
        /* returns the csrf credentails for ajax */
        public function get_csrf() {
            echo json_encode($this->General->get_csrf());
        }
        /* Handles filtering of all pages from search and category switching */
        public function filter($page) {
            $this->load->model('Dashboard');
            $post = $this->General->clean();
            $page = $this->General->clean($page);
            if ((((!$this->Dashboard->check_not_admin() && $page == 'dashboard') || (!$this->Dashboard->check_not_admin() && $page == 'order') || $page == 'catalogue')) && !empty($post) && in_array($page, array('dashboard', 'catalogue', 'order'))) {
                echo json_encode($this->General->filter($post, $page));
            }
        }
    }
?>