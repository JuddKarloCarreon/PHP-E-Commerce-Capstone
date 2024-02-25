<?php
    class Database extends CI_Model {
        public $product_types = array('Vegetables', 'Fruits', 'Pork', 'Beef', 'Chicken');
        public $order_status = array('Pending', 'On-Process', 'Shipped', 'Delivered');
        public $item_limit = 10;
        public $shipping_fee = '5.00';
        
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
        public function count_records_search($type = 0, $search = '', $kind = 'products') {
            $query = "SELECT COUNT($kind.id) AS count FROM $kind";
            $where = '';
            $values = array();
            if ($kind == 'products') {
                $where = 'WHERE active=?';
                $values = array(1);
            } else {
                $query .= " LEFT JOIN checkout_details AS t2 ON $kind.shipping_id=t2.id";
            }
            if ($type != 0) { 
                if ($kind == 'products') {
                    $where .= " AND product_type=?";
                } else {
                    if ($where == '') {
                        $where = 'WHERE';
                    } else {
                        $where .= ' AND';
                    }
                    $where .= " $kind.status=?";
                }
                array_push($values, $type);
            }
            if ($search != '') {
                if ($where == '') {
                    $where = 'WHERE';
                } else {
                    $where .= ' AND';
                }
                if ($kind == 'products') {
                    $where .= " name LIKE ?";
                } else {
                    $where .= " CONCAT(t2.first_name, ' ', t2.last_name) LIKE ?";
                }
                array_push($values, "%$search%");
            }
            $count = $this->db->query("$query $where", $values)->row_array()['count'];
            return $count;
        }
        public function add_record($table, $post) {
            if ($this->count_records('users') == 0 && $table == 'users') {
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
            // return array($query, array_values($post));
            $this->db->query($query, array_values($post));
            return $this->db->insert_id();
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
        public function delete_record($table, $id) {
            if (!is_array($id)) {
                $id = array('id' => $id);
            }
            $query = "DELETE FROM $table WHERE " . array_key_first($id) . "=?";
            $this->db->query($query, array_values($id));
        }
        public function validate_id($id) {
            $id = $this->General->clean($id);
            return filter_var($id, FILTER_VALIDATE_INT);
        }
        public function get_cart_records() {
            $query = "SELECT t1.*, t1.amount * t2.price AS total, IF(t2.image_names_json = '[]', 'close.svg', CONCAT('products/',t1.product_id,'/',SUBSTRING(SUBSTRING(t2.image_names_json, 3),1,POSITION('" . '"' . "' IN SUBSTRING(t2.image_names_json, 3)) - 1))) AS img, t2.name, t2.price, t2.stock, t2.active FROM cart_items AS t1 LEFT JOIN products AS t2 ON t1.product_id=t2.id WHERE t1.user_id=?";
            return $this->db->query($query, array($this->session->userdata('user')['id']))->result_array();
        }
        public function get_order_records($filt = array(), $page = 1, $limit='', $s_val='') {
            $values = array();
            $where = '';
            if (array_key_exists('status', $filt)) {
                $filt['status'] = intval($filt['status']);
                if ($filt['status'] == 0) {
                    unset($filt['status']);
                }
            }
            if (!empty($filt)) {
                $where = 'WHERE';
                foreach ($filt as $key => $val) {
                    $where .= " $key=?";
                    array_push($values, $val);
                    if (end($filt) != $val) {
                        $where .= ' AND';
                    }
                }
            }
            if (!in_array($s_val, array('', '%%'))) {
                if ($where != '') {
                    $where .= ' AND';
                } else {
                    $where = 'WHERE';
                }
                $where .= " CONCAT(t2.first_name, ' ', t2.last_name) LIKE ?";
                array_push($values, "%$s_val%");
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
            $query = "SELECT t1.*, DATE_FORMAT(t1.created_at, '%m-%d-%Y') AS order_date, CONCAT(t2.first_name, ' ', t2.last_name) AS name, CONCAT(t2.address_1, ', ', t2.address_2, ', ', t2.city, ', ', t2.state, ' ', t2.zip) AS address FROM orders as t1 LEFT JOIN checkout_details as t2 ON t1.shipping_id=t2.id $where $lim";
            return $this->db->query($query,$values)->result_array();
        }
    }
?>