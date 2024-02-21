<?php
    class Database extends CI_Model {
        public function get_records($table, $field = 1, $value = 1) {
            $query = "SELECT * FROM $table WHERE $field=?";
            return $this->db->query($query, array($value))->result_array();
        }
        public function get_record($table, $field, $value, $order = '') {
            $query = "SELECT * FROM $table WHERE $field=? $order";
            return $this->db->query($query, array($value))->row_array();
        }
        public function count_records($table, $field = 1, $value = 1) {
            $query = "SELECT COUNT(id) as count FROM $table WHERE $field=?";
            return $this->db->query($query, array($value))->row_array()['count'];
        }
        public function add_record($table, $post) {
            if ($this->count_records('users') == 0) {
                $post['is_admin'] = 1;
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
        public function delete_record($table, $id) {
            $query = "DELETE FROM $table WHERE id=?";
            $values = array($id);
            $this->db->query($query, $values);
        }
        public function validate_id($id) {
            $id = $this->Defence->clean($id);
            return filter_var($id, FILTER_VALIDATE_INT);
        }
    }
?>