<?php
$DB_HOST = 'localhost';      
$DB_NAME = 'php_api';        
$DB_USER = 'root';          
$DB_PASS = '';               
$DB_DSN  = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();
