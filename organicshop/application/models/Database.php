<?php
    class Database extends CI_Model {
        public $product_types = array('Vegetables', 'Fruits', 'Pork', 'Beef', 'Chicken');
        public $item_limit = 10;

        private function products_where($table, $value) {
            if ($table == 'products') {
                return array('active=? AND', array(1, $value));
            }
            return array('', array($value));
        }
        public function get_records($table, $field = 1, $value = 1, $not_f = 1, $not_v = 0, $page = 1, $limit=$this->item_limit) {
            list($where, $values) = $this->products_where($table, $value);
            array_push($values, $not_v, $limit * ($page - 1));
            $query = "SELECT * FROM $table WHERE $where $field=? AND $not_f!=? LIMIT $limit OFFSET ?";
            return $this->db->query($query, $values)->result_array();
        }
        public function get_record($table, $field, $value, $order = '') {
            list($where, $values) = $this->products_where($table, $value);
            $query = "SELECT * FROM $table WHERE $where $field=? $order";
            return $this->db->query($query, $values)->row_array();
        }
        public function count_records($table, $field = 1, $value = 1) {
            list($where, $values) = $this->products_where($table, $value);
            $query = "SELECT COUNT(id) as count FROM $table WHERE $where $field=?";
            return $this->db->query($query, $values)->row_array()['count'];
        }
        public function add_record($table, $post) {
            if ($this->count_records('users') == 0) {
                $post['user_level'] = 1;
            }
            $query = "INSERT INTO $table(" . implode(', ', array_keys($post)) . ") VALUES(";
            for ($i = 0; $i < count($post); $i++) {
                $query .= '?';
                if ($i != count($post) - 1) {
                    $query .= ', ';
                } else {
                    $query .= ')';
                }
            }
            $this->db->query($query, array_values($post));
        }
        public function update_record($table, $id, $data) {
            $query = "UPDATE $table SET";
            $values = array();
            $last_key = array_keys(array_slice($data, -1))[0];
            foreach ($data as $key => $val) {
                $query .= " $key=?";
                array_push($values, $val);
                if ($key !== $last_key) {
                    $query .= ', ';
                }
            }
            $query .= ' WHERE id=?';
            array_push($values, $id);
            $this->db->query($query, $values);
        }
        public function soft_delete_record($table, $id) {
            $query = "UPDATE $table SET active=? WHERE id=?";
            $values = array(0, $id);
            $this->db->query($query, $values);
        }
        public function validate_id($id) {
            $id = $this->General->clean($id);
            return filter_var($id, FILTER_VALIDATE_INT);
        }
    }
?>