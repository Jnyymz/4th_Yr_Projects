<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$role = $_SESSION['user']['role'];
$backPage = match ($role) {
  'admin' => 'admin_dashboard.php',
  'superadmin' => 'superadmin_dashboard.php',
  default => 'user_dashboard.php'
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Notifications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">🔔 Notifications</h3>
    <a href="<?= $backPage ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <div id="notifContainer">
    <p class="text-muted text-center">Loading notifications...</p>
  </div>
</div>

<script>
fetch("api.php?action=getNotifications")
  .then(res => res.json())
  .then(data => {
    const c = document.getElementById("notifContainer");
    c.innerHTML = data.map(n => `
      <div class="card mb-3 p-3">
        <h6 class="text-${n.type === 'order' ? 'success':'primary'}">${n.type.toUpperCase()}</h6>
        <p>${n.message}</p>
        <small class="text-muted">${n.date_created}</small>
      </div>
    `).join('');
  });
</script>
</body>
</html>
