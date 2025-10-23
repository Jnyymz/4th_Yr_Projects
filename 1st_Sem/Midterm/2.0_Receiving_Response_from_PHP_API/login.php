<?php require 'config.php';
if (!empty($_SESSION['user'])) {
    header('Location: index.php'); exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.34.0/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Login</h3>
          <form id="loginForm" novalidate>
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input id="password" name="password" type="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100" type="submit">Login</button>
            <div class="mt-3 text-center"><a href="register.php">Create account</a></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;
  if (!username || !password) {
    Swal.fire('Missing', 'Please fill all fields', 'warning');
    return;
  }

  const res = await fetch('api.php?action=login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({username, password})
  });
  const data = await res.json();
  if (data.success) {
    window.location.href = 'index.php';
  } else {
    Swal.fire('Login failed', data.message || 'Invalid credentials', 'error');
  }
});
</script>
</body>
</html>
