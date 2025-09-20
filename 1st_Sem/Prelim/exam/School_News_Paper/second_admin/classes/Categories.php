<?php
require_once __DIR__ . '/Database.php';

class Categories extends Database {

    public function addCategory($name) {
        $sql = "INSERT INTO article_categories (name) VALUES (?)";
        return $this->executeNonQuery($sql, [$name]);
    }

    public function getAll() {
        $sql = "SELECT category_id, name, created_at 
                FROM article_categories ORDER BY created_at DESC";
        return $this->executeQuery($sql);
    }
}
