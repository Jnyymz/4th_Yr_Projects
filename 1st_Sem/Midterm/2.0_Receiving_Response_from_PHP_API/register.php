<?php require 'config.php'; 
// if user already logged in -> redirect to index.php
if (!empty($_SESSION['user'])) {
    header('Location: index.php'); exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.34.0/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Register</h3>
          <form id="registerForm" novalidate>
            <div class="mb-2">
              <label class="form-label">Username</label>
              <input id="username" name="username" class="form-control" required>
              <div id="usernameHelp" class="form-text">Username must be unique.</div>
            </div>
            <div class="mb-2">
              <label class="form-label">First name</label>
              <input id="firstname" name="firstname" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Last name</label>
              <input id="lastname" name="lastname" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Password</label>
              <input id="password" name="password" type="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" value="1" id="is_admin" name="is_admin">
              <label class="form-check-label" for="is_admin">Register as admin</label>
            </div>

            <button class="btn btn-primary w-100" type="submit">Create account</button>
            <div class="mt-3 text-center">
              <a href="login.php">Already have an account? Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const apiUrl = 'api.php?action=register';

document.getElementById('registerForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('username').value.trim();
  const firstname = document.getElementById('firstname').value.trim();
  const lastname = document.getElementById('lastname').value.trim();
  const password = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  const is_admin = document.getElementById('is_admin').checked ? 1 : 0;

  // client-side quick validation
  if (!username || !firstname || !lastname || !password || !confirm) {
    Swal.fire('Missing fields', 'Please fill all fields', 'warning');
    return;
  }
  if (password.length < 8) {
    Swal.fire('Weak password', 'Password must be at least 8 characters', 'warning');
    return;
  }
  if (password !== confirm) {
    Swal.fire('Mismatch', 'Passwords do not match', 'warning');
    return;
  }

  // check username availability
  try {
    const check = await fetch(`api.php?action=check_username&username=${encodeURIComponent(username)}`);
    const checkData = await check.json();
    if (!checkData.available) {
      Swal.fire('Username taken', 'Please choose a different username', 'error');
      return;
    }
  } catch (err) {
    console.error(err);
  }

  // send register request
  const res = await fetch(apiUrl, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({username, firstname, lastname, password, confirm_password: confirm, is_admin})
  });
  const data = await res.json();
  if (data.success) {
    Swal.fire('Welcome!', 'Account created', 'success').then(() => {
      window.location.href = 'index.php';
    });
  } else {
    Swal.fire('Error', data.message || 'Something went wrong', 'error');
  }
});
</script>
</body>
</html>
