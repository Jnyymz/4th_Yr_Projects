<?php
require 'config.php';
if (empty($_SESSION['user'])) {
    header('Location: login.php'); exit;
}
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.34.0/minified.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">MyApp</a>
    <div class="ms-auto">
      <span class="me-3">Role: <?= $user['is_admin'] ? 'Admin' : 'User' ?></span>
      <a href="all_users.php" class="btn btn-outline-primary btn-sm me-2">All users</a>
      <button id="logoutBtn" class="btn btn-danger btn-sm">Logout</button>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1>Hello there <?= htmlspecialchars($user['username']) ?> 👋</h1>
  <p>Welcome back, <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>.</p>
</div>

<script>
document.getElementById('logoutBtn').addEventListener('click', async () => {
  await fetch('api.php?action=logout');
  window.location.href = 'login.php';
});
</script>
</body>
</html>
