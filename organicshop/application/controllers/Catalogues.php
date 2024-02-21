<?php
    class Catalogues extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('Catalogue');
        }
        public function index() {
            $param = $this->Catalogue->get_param();
            $this->load->view('dashboards/catalogue', $param);
        }
    }
?>