<?php
class Database {
    private $host = "localhost";
    private $dbname = "fiverrdb";  // same DB as before
    private $username = "root";
    private $password = "";
    private $conn;

    public function connect() {
        if ($this->conn == null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname}",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }

    public function executeQuery($query, $params = []) {
        $stmt = $this->connect()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function executeNonQuery($query, $params = []) {
        $stmt = $this->connect()->prepare($query);
        return $stmt->execute($params);
    }
}
