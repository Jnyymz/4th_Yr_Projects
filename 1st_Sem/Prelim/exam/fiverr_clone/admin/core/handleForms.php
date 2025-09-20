<?php
session_start();
require_once "../classes/Categories.php";
require_once "../classes/User.php";

$categories = new Categories();
$user = new User();

// Add category
if (isset($_POST['addCategory'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $categories->addCategory($name);
        header("Location: ../index.php?success=Category added");
        exit;
    } else {
        header("Location: ../index.php?error=Category name required");
        exit;
    }
}

// Register
if (isset($_POST['register'])) {
    $user->register($_POST['username'], $_POST['email'], $_POST['password']);
    header("Location: ../login.php?success=Registered successfully");
    exit;
}

// Login
if (isset($_POST['login'])) {
    if ($user->login($_POST['email'], $_POST['password'])) {
        header("Location: ../index.php");
    } else {
        header("Location: ../login.php?error=Invalid credentials");
    }
    exit;
}
