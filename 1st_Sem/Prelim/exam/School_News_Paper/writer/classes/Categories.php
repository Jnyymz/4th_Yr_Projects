<?php
require_once __DIR__ . '/Database.php'; // Adjust path if needed

/**
 * Categories class for writers to retrieve categories
 */
class Categories extends Database
{
    /**
     * Get all article categories
     * @return array
     */
    public function getAllCategories()
    {
        $sql = "SELECT category_id, name, created_at 
                FROM article_categories 
                ORDER BY created_at DESC";
        return $this->executeQuery($sql);
    }

    /**
     * Get a single category by ID
     * @param int $id
     * @return array|null
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT category_id, name, created_at 
                FROM article_categories 
                WHERE category_id = ?";
        return $this->executeQuerySingle($sql, [$id]);
    }
}
?>
