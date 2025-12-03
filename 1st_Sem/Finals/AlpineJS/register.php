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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.1/cdn.min.js" defer></script>
</head>

<body class="bg-gray-50">

  <div class="container py-5" x-data="registerApp()">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4">
          <h3 class="mb-3">Register</h3>

          <form @submit.prevent="submitRegister">

            <div class="mb-2">
              <label>Username</label>
              <input class="form-control" x-model="username" required>
            </div>

            <div class="mb-2">
              <label>First name</label>
              <input class="form-control" x-model="firstname" required>
            </div>

            <div class="mb-2">
              <label>Last name</label>
              <input class="form-control" x-model="lastname" required>
            </div>

            <div class="mb-2">
              <label>Password</label>
              <input type="password" class="form-control" x-model="password" required>
            </div>

            <div class="mb-3">
              <label>Role</label>
              <select class="form-control" x-model="role" required>
                <option value="user">User</option>
                <option value="superadmin">Super Admin</option>
              </select>
            </div>

            <button class="btn btn-primary w-100" type="submit">Register</button>

            <a href="login.php" class="btn btn-link w-100 mt-2">
              Already have an account? Login
            </a>

          </form>

        </div>
      </div>
    </div>
  </div>


<script>
function openSwalAdjust(callback) {
    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;

    if (scrollBarWidth > 0)
        document.body.style.paddingRight = scrollBarWidth + "px";

    document.body.style.overflow = "hidden";

    return callback().finally(() => {
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";
    });
}

function registerApp() {
  return {

    username: "",
    firstname: "",
    lastname: "",
    password: "",
    role: "user",

    async submitRegister() {
      const data = {
        username: this.username.trim(),
        firstname: this.firstname.trim(),
        lastname: this.lastname.trim(),
        password: this.password,
        role: this.role
      };

      openSwalAdjust(async () => {
        Swal.showLoading();

        try {
          const response = await fetch("api.php?action=register", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
          });

          const result = await response.json();
          Swal.close();

          if (result.success) {
            Swal.fire({
              icon: "success",
              title: "Registered",
              text: result.message
            }).then(() => location.href = "login.php");

          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: result.message
            });
          }

        } catch (err) {
          Swal.close();
          Swal.fire({
            icon: "error",
            title: "Error",
            text: err.message || "Unknown error"
          });
        }
      });

    }
  };
}
</script>

</body>
</html>
