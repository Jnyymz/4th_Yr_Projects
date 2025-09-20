<?php
require_once __DIR__ . '/Database.php';

class User extends Database {

    public function register($username, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO school_publication_users (username, email, password, is_admin) 
                VALUES (?, ?, ?, 1)";
        return $this->executeNonQuery($sql, [$username, $email, $hash]);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM school_publication_users WHERE email = ?";
        $user = $this->executeQuerySingle($sql, [$email]);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
