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
    

    public function createArticle($title, $content, $author_id, $image_path = null) {
        $sql = "INSERT INTO articles (title, content, author_id, image_path, is_active) VALUES (?, ?, ?, ?, 1)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $image_path]);
    }



    /**
     * Retrieves articles from the database.
     * @param int|null $id The article ID to retrieve, or null for all articles.
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT * FROM articles WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM articles 
                JOIN school_publication_users ON 
                articles.author_id = school_publication_users.user_id 
                ORDER BY articles.created_at DESC";

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
        $sql = "SELECT * FROM articles 
                JOIN school_publication_users ON 
                articles.author_id = school_publication_users.user_id
                WHERE author_id = ? ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    // I added this
    public function insertArticle($title, $content, $author_id) {
        return $this->createArticle($title, $content, $author_id);
    }


    /**
     * Updates an article.
     * @param int $id The article ID to update.
     * @param string $title The new title.
     * @param string $content The new content.
     * @return int The number of affected rows.
     */
    public function updateArticle($id, $title, $content) {
        $sql = "UPDATE articles SET title = ?, content = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $id]);
    }
    
    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }


    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */


    public function deleteArticle($article_id) {
        // 1. Copy article details into deleted_articles
        $sqlInsert = "INSERT INTO deleted_articles (article_id, user_id, title)
                    SELECT article_id, author_id, title
                    FROM articles
                    WHERE article_id = ?";
        $this->executeNonQuery($sqlInsert, [$article_id]);

        // 2. Delete from articles
        $sqlDelete = "DELETE FROM articles WHERE article_id = ?";
        $this->executeNonQuery($sqlDelete, [$article_id]);
    }


    //  Send access request
    public function requestAccess($article_id, $requester_id, $owner_id) {
        $sql = "INSERT INTO article_access_requests (article_id, requester_id, owner_id, status) 
                VALUES (?, ?, ?, 'pending')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$article_id, $requester_id, $owner_id]);
    }

    //  Get request status
    public function getRequestStatus($article_id, $user_id) {
        $sql = "SELECT status FROM article_access_requests 
                WHERE article_id = ? AND requester_id = ? 
                ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$article_id, $user_id]);
        return $stmt->fetchColumn();
    }

    // Owner handles request
    public function updateRequestStatus($request_id, $status) {
        $sql = "UPDATE article_access_requests SET status = ? WHERE request_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $request_id]);
    }

    //  Get pending requests for owner
    public function getPendingRequests($owner_id) {
        $sql = "SELECT r.request_id, r.article_id, a.title, u.username, r.status
                FROM article_access_requests r
                JOIN articles a ON r.article_id = a.article_id
                JOIN school_publication_users u ON r.requester_id = u.user_id
                WHERE r.owner_id = ? AND r.status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteArticleWithImage($article_id) {
        $stmt = $this->conn->prepare("SELECT image_path FROM articles WHERE article_id = ?");
        $stmt->execute([$article_id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            $deleteStmt = $this->conn->prepare("DELETE FROM articles WHERE article_id = ?");
            if ($deleteStmt->execute([$article_id])) {
                // If image exists, delete it
                if (!empty($article['image_path']) && file_exists("../" . $article['image_path'])) {
                    unlink("../" . $article['image_path']);
                }
                return true;
            }
        }
        return false;
    }


}
?>