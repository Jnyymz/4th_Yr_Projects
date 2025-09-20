<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    /**
     * Creates a new article.
     * @param string $title The article title.
     * @param string $content The article content.
     * @param int $author_id The ID of the author.
     * @return int The ID of the newly created article.
     */
    public function createArticle($title, $content, $author_id, $category_id = null, $image_path = null) {
        $sql = "INSERT INTO articles 
                (title, content, author_id, category_id, image_path, is_active) 
                VALUES (?, ?, ?, ?, ?, 0)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id, $image_path]);
    }



    /**
     * Retrieves articles from the database.
     * @param int|null $id The article ID to retrieve, or null for all articles.
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.name AS category_name
                    FROM articles a
                    JOIN school_publication_users u ON a.author_id = u.user_id
                    LEFT JOIN article_categories c ON a.category_id = c.category_id
                    WHERE a.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }

    $sql = "SELECT a.*, u.username, c.name AS category_name
            FROM articles a
            JOIN school_publication_users u ON a.author_id = u.user_id
            LEFT JOIN article_categories c ON a.category_id = c.category_id
            ORDER BY a.created_at DESC";
    return $this->executeQuery($sql);
}

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM articles 
                JOIN school_publication_users ON 
                articles.author_id = school_publication_users.user_id 
                WHERE is_active = 1 ORDER BY articles.created_at DESC";
                
        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT a.*, u.username, c.name AS category_name
                FROM articles a
                JOIN school_publication_users u ON a.author_id = u.user_id
                LEFT JOIN article_categories c ON a.category_id = c.category_id
                WHERE a.author_id = ?
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Updates an article.
     * @param int $id The article ID to update.
     * @param string $title The new title.
     * @param string $content The new content.
     * @return int The number of affected rows.
     */
    public function updateArticle($id, $title, $content, $image_path = null) {
        if ($image_path) {
            $sql = "UPDATE articles SET title = ?, content = ?, image_path = ? WHERE article_id = ?";
            return $this->executeNonQuery($sql, [$title, $content, $image_path, $id]);
        } else {
            $sql = "UPDATE articles SET title = ?, content = ? WHERE article_id = ?";
            return $this->executeNonQuery($sql, [$title, $content, $id]);
        }
    }

    
    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $userModel = new User();
        if (!$userModel->isAdmin()) {
            return 0;
        }
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [(int)$is_active, $id]);
    }


    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    // requestAccess
    public function requestAccess($article_id, $requester_id, $owner_id) {
        $sql = "INSERT INTO article_access_requests (article_id, requester_id, owner_id, status) 
                VALUES (?, ?, ?, 'pending')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$article_id, $requester_id, $owner_id]);
    }


    // getRequestStatus
    public function getRequestStatus($article_id, $user_id) {
        $sql = "SELECT status FROM article_access_requests 
                WHERE article_id = ? AND requester_id = ? 
                ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$article_id, $user_id]);
        return $stmt->fetchColumn();
    }

    public function updateRequestStatus($request_id, $status) {
        $sql = "UPDATE article_access_requests SET status = ? WHERE request_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $request_id]);
    }

    // getPendingRequests
    public function getPendingRequests($owner_id) {
        $sql = "SELECT r.request_id, r.article_id, a.title, u.username, r.status
                FROM article_access_requests r
                JOIN articles a ON r.article_id = a.article_id
                JOIN school_publication_users u ON r.requester_id = u.user_id
                WHERE r.owner_id = ? AND r.status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inside Article.php
    /**
     * Get all articles the user has access to (accepted requests).
     * @param int $user_id The ID of the current user.
     * @return array
     */
    public function getAccessedArticles($userId) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.username
            FROM articles a
            INNER JOIN school_publication_users u ON a.author_id = u.user_id
            INNER JOIN article_access_requests ar ON a.article_id = ar.article_id
            WHERE ar.requester_id = :userId
            AND ar.status = 'accepted'
            ORDER BY a.created_at DESC
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeletedArticles($userId) {
        $stmt = $this->pdo->prepare("
            SELECT title, deleted_at
            FROM deleted_articles
            WHERE user_id = ?
            ORDER BY deleted_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
?>
