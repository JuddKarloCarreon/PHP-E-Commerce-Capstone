<?php
    class Database extends CI_Model {
        public function get_records($table, $field = 1, $value = 1) {
            $query = "SELECT * FROM $table WHERE $field=?";
            return $this->db->query($query, array($value))->result_array();
        }
        public function get_record($table, $field, $value) {
            $query = "SELECT * FROM $table WHERE $field=?";
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
    }
?>