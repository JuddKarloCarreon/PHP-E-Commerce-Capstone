<?php
    class Database extends CI_Model {
        public $product_types = array('Vegetables', 'Fruits', 'Pork', 'Beef', 'Chicken');
        public $item_limit = 1;
        
        private function products_where($table) {
            if ($table == 'products') {
                return array('active=?', array(1));
            }
            return array('', array());
        }
        public function get_records($table, $field = 1, $value = 1, $not_f = 1, $not_v = 0, $page = 1, $limit='', $s_field = '', $s_val='') {
            list($where, $values) = $this->products_where($table);
            $fields = array($field, $not_f);
            $vals = array($value, $not_v);
            foreach ($fields as $key => $field) {
                if ($field != 1) {
                    $where .= $this->and_add($where);
                    $where .= " $field";
                    if ($key == 0) {
                        $where .= '=?';
                    } else {
                        $where .= '!=?';
                    }
                    array_push($values, $vals[$key]);
                }
            }
            if (!in_array($s_val, array('', '%%'))) {
                $where .= $this->and_add($where);
                $where .= " $s_field LIKE ?";
                array_push($values, $s_val);
            }
            if ($limit == 'all') {
                $lim = '';
            } else {
                if ($limit == '') {
                    $limit = $this->item_limit;
                }
                $offset = $limit * ($page - 1);
                if ($offset < 0) {
                    $offset = 0;
                }
                $lim = "LIMIT $limit OFFSET ?";
                array_push($values, $offset);
            }
            $query = "SELECT * FROM $table WHERE $where $lim";
            return $this->db->query($query, $values)->result_array();
        }
        public function get_record($table, $field, $value, $order = '') {
            list($where, $values) = $this->products_where($table);
            $where .= $this->and_add($where);
            if (!is_array($field)) {
                $field = array($field);
                $value = array($value);
            }
            foreach ($field as $key => $f) {
                $where .= " $f=?";
                if (end($field) != $f) {
                    $where .= ' AND';
                }
                array_push($values, $value[$key]);
            }
            $query = "SELECT * FROM $table WHERE $where $order";
            return $this->db->query($query, $values)->row_array();
        }
        public function search_string($table, $field, $value, $field2 = 1, $value2 = 1) {
            list($where, $values) = $this->products_where($table);
            if ($field2 != 1) {
                $where .= $this->and_add($where);
                $where .= " $field2=?";
                array_push($values, $value2);
            }
            if ($value != '') {
                $where .= $this->and_add($where);
                $where .= " $field LIKE ?";
                array_push($values, $value);
            }
            $query = "SELECT * FROM $table WHERE $where";
            $values = array($value, $value2);
            return $this->db->query($query, $values)->result_array();
        }
        private function and_add($where) {
            if ($where != '') {
                return ' AND';
            }
            return '';
        }
        public function count_records($table, $field = 1, $value = 1) {
            list($where, $values) = $this->products_where($table);
            if ($field != 1) {
                $where .= $this->and_add($where);
                $where .= " $field=?";
                array_push($values, $value);
            }
            $query = "SELECT COUNT(id) as count FROM $table";
            if ($where != '') {
                $query .=  " WHERE $where";
            }
            return $this->db->query($query, $values)->row_array()['count'];
        }
        public function count_products($type = 0, $search = '') {
            $query = "SELECT COUNT(id) AS count FROM products WHERE active=?";
            $values = array(1);
            if ($type != 0) {
                $query .= " AND product_type=?";
                array_push($values, $type);
            }
            if ($search != '') {
                $query .= " AND name LIKE ?";
                array_push($values, "%$search%");
            }
            $count = $this->db->query($query, $values)->row_array()['count'];
            return $count;
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