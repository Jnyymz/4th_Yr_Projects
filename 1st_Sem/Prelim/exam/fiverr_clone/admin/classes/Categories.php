<?php
require_once "Database.php";

class Categories {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addCategory($name) {
        $sql = "INSERT INTO categories (category_name) VALUES (?)";
        return $this->db->executeNonQuery($sql, [$name]);
    }

    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY date_added DESC";
        return $this->db->executeQuery($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
