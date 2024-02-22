<?php
    class General extends CI_Model {
        /* Loads the security helper for all methods in this model */
        public function __construct() {
            parent::__construct();
            $this->load->helper('security');
            
        }
        /* Cleans data. If no data passed, returns cleaned post data */
        public function clean($data = 'post') {
            if ($data === 'post') {
                return $this->input->post(NULL, TRUE);
            }
            return $this->security->xss_clean($data);
        }
        /* Returns necassary csrf security credentials */
        public function get_csrf() {
            return array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
        }
        public function search_products($post, $data) {
            if ($post['search'] == '') {
                return $data;
            }
            $results = array();
            foreach ($data as $row) {
                if (strpos(strtolower($row['name']), strtolower($post['search'])) !== FALSE) {
                    array_push($results, $row);
                }
            }
            return $results;
        }
    }
?>