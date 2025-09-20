<?php
require_once 'Database.php';

class Categories extends Database {

    // Get all categories
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY date_added DESC";
        return $this->executeQuery($sql);
    }

    // Get single category by ID
    public function getCategoryById($category_id) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        return $this->executeQuerySingle($sql, [$category_id]);
    }

    // Add a new category
    public function addCategory($category_name) {
        $sql = "INSERT INTO categories (category_name) VALUES (?)";
        return $this->executeNonQuery($sql, [$category_name]);
    }

    // Delete a category
    public function deleteCategory($category_id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_id]);
    }
}
?>
