<?php
require_once "Database.php";

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($username, $email, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO fiverr_clone_users (username, email, password, is_client)
                VALUES (?, ?, ?, 0)";
        return $this->db->executeNonQuery($sql, [$username, $email, $hashed]);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM fiverr_clone_users WHERE email = ?";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['user_id'];
            return true;
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
}
