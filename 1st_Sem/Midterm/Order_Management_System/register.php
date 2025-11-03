<?php
// register.php
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
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4">
          <h3 class="mb-3">Register</h3>
          <form id="registerForm">
            <div class="mb-2">
              <label>Username</label>
              <input class="form-control" name="username" required>
            </div>
            <div class="mb-2">
              <label>First name</label>
              <input class="form-control" name="firstname" required>
            </div>
            <div class="mb-2">
              <label>Last name</label>
              <input class="form-control" name="lastname" required>
            </div>
            <div class="mb-2">
              <label>Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
              <label>Role</label>
              <select class="form-control" name="role" id="roleSelect" required>
                <option value="user">User</option>
                <option value="superadmin">Super Admin</option>
              </select>
            </div>

            <button class="btn btn-primary w-100" type="submit">Register</button>
            <a href="login.php" class="btn btn-link w-100 mt-2">Already have an account? Login</a>
          </form>
        </div>
      </div>
    </div>
  </div>

<script>
/* ---------- helper to avoid page shift when SweetAlert shows ---------- */
function openSwalAdjust(promiseFn) {
  // compute scrollbar width
  const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
  if (scrollBarWidth > 0) {
    document.body.style.paddingRight = scrollBarWidth + 'px';
  }
  document.body.style.overflow = 'hidden';
  return promiseFn().finally(() => {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  });
}

/* ---------- register submit ---------- */
document.getElementById('registerForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(this);
  const data = {
    username: fd.get('username').trim(),
    firstname: fd.get('firstname').trim(),
    lastname: fd.get('lastname').trim(),
    password: fd.get('password'),
    role: fd.get('role')
  };

  await openSwalAdjust(async () => {
    Swal.showLoading();
    try {
      const res = await fetch('api.php?action=register', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(data)
      });
      const j = await res.json();
      if (j.success) {
        Swal.fire({icon:'success',title:'Registered',text:j.message}).then(()=> location.href='login.php');
      } else {
        Swal.fire({icon:'error',title:'Error',text:j.message});
      }
    } catch (err) {
      Swal.fire({icon:'error',title:'Error',text:err.message});
    }
  });

});
</script>
</body>
</html>
