<?php
// login.php
session_start();
if (!empty($_SESSION['user'])) {
    header('Location: dashboard_redirect.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.1/cdn.min.js" defer></script>
</head>

<body class="bg-gray-50">

  <div class="container py-5" x-data="loginApp()">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card p-4">
          <h3 class="mb-3">Login</h3>

          <form @submit.prevent="submitLogin">
            <div class="mb-2">
              <label>Username</label>
              <input class="form-control" x-model="username" required>
            </div>

            <div class="mb-2">
              <label>Password</label>
              <input type="password" class="form-control" x-model="password" required>
            </div>

            <button class="btn btn-primary w-100" type="submit">Login</button>
            <a href="register.php" class="btn btn-link w-100 mt-2">Register</a>
          </form>

        </div>
      </div>
    </div>
  </div>


<script>
function openSwalAdjust(promiseFn) {
    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
    if (scrollBarWidth > 0) document.body.style.paddingRight = scrollBarWidth + 'px';
    document.body.style.overflow = 'hidden';

    return promiseFn().finally(() => {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
}
function loginApp() {
  return {
    username: "",
    password: "",

    async submitLogin() {
      const data = {
        username: this.username.trim(),
        password: this.password
      };

      openSwalAdjust(async () => {
        Swal.showLoading();

        try {
          const response = await fetch('api.php?action=login', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
          });

          const j = await response.json();
          Swal.close();

          if (j.success) {
            Swal.fire({
              icon: 'success',
              title: 'Welcome',
              text: 'Login successful'
            }).then(() => {
              if (j.user.role === 'superadmin') location.href = 'superadmin_dashboard.php';
              else if (j.user.role === 'admin') location.href = 'admin_dashboard.php';
              else location.href = 'user_dashboard.php';
            });

          } else {
            Swal.fire({
              icon: 'error',
              title: 'Login failed',
              text: j.message
            });
          }

        } catch (err) {
          Swal.close();
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message || "Unknown error"
          });
        }

      });
    }
  }
}
</script>

</body>
</html>
