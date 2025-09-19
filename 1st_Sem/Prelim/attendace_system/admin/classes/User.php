<?php

require_once __DIR__ . '/Database.php';

class User extends Database {
    public $id;
    public $username;
    public $role;

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username=? AND role='admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result['password'])) {
            $this->id = $result['id'];
            $this->username = $result['username'];
            $this->role = $result['role'];
            return true;
        }
        return false;
    }
}
