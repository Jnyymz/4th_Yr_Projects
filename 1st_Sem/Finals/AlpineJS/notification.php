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
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-light p-4">
<div class="container" 
     x-data="notifApp()" 
     x-init="loadNotifications()">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">🔔 Notifications</h3>
    <a href="<?= $backPage ?>" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <!-- Notifications Container -->
  <div>
    <!-- Loading State -->
    <template x-if="loading">
      <p class="text-muted text-center">Loading notifications...</p>
    </template>

    <!-- No Data -->
    <template x-if="!loading && notifications.length === 0">
      <p class="text-muted text-center">No notifications available.</p>
    </template>

    <!-- Notification Cards -->
    <template x-for="n in notifications" :key="n.id">
      <div class="card mb-3 p-3">
        <h6 :class="'text-' + (n.type === 'order' ? 'success' : 'primary')">
          <span x-text="n.type.toUpperCase()"></span>
        </h6>
        <p x-text="n.message"></p>
        <small class="text-muted" x-text="n.date_created"></small>
      </div>
    </template>
  </div>
</div>

<!-- Alpine Script -->
<script>
function notifApp() {
  return {
    notifications: [],
    loading: true,

    async loadNotifications() {
      try {
        const res = await fetch('api.php?action=getNotifications');
        if (!res.ok) throw new Error('Network error');

        const data = await res.json();
        this.notifications = data ?? [];
      } 
      catch (e) {
        Swal.fire('Error', 'Failed to load notifications', 'error');
      }
      finally {
        this.loading = false;
      }
    }
  }
}
</script>

</body>
</html>
