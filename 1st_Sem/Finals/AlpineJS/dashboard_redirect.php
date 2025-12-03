<?php
session_start();
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['user']['role'] ?? 'user';
if ($role === 'superadmin') header('Location: superadmin_dashboard.php');
elseif ($role === 'admin') header('Location: admin_dashboard.php');
else header('Location: user_dashboard.php');
exit;
