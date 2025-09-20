<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Categories.php';

$userObj = new User();
$catObj  = new Categories();

/* ────────── Registration ────────── */
if (isset($_POST['registerBtn'])) {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $pass     = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($pass === $confirm) {
        try {
            $userObj->register($username, $email, $pass);
            header("Location: ../login.php?msg=registered");
            exit;
        } catch (PDOException $e) {
            echo "❌ Registration failed: " . $e->getMessage();
        }
    } else {
        echo "⚠️ Passwords do not match.";
    }
}

/* ────────── Login ────────── */
if (isset($_POST['loginBtn'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $user = $userObj->login($email, $pass);
    if ($user && $user['is_admin'] == 1) {
        $_SESSION['second_admin'] = $user['user_id'];
        header("Location: ../index.php");
        exit;
    } else {
        echo "❌ Invalid credentials or not an admin.";
    }
}

/* ────────── Logout ────────── */
if (isset($_POST['logoutBtn'])) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

/* ────────── Add Category ────────── */
if (isset($_POST['insertCategoryBtn'])) {
    if (!isset($_SESSION['second_admin'])) {
        echo "⚠️ Unauthorized.";
        exit;
    }
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        try {
            $catObj->addCategory($name);
            header("Location: ../index.php?msg=added");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "⚠️ Category already exists.";
            } else {
                echo "❌ Error: " . $e->getMessage();
            }
        }
    } else {
        echo "⚠️ Category name required.";
    }
}
