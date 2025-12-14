<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background:
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url("https://i.pinimg.com/736x/e1/89/8d/e1898d3c2d18042aad07e8e7f154ac9c.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    }

    .glass-box {
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(12px);
    border-radius: 24px;
    padding: 3rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    animation: fadeInUp 1s ease forwards;
    opacity: 0;
    }

    .form-control {
    background: rgba(255,255,255,0.25);
    border: none;
    color: #fff;
    }
    .form-control::placeholder { color: #e5e7eb; }
    .form-control:focus {
    background: rgba(255,255,255,0.35);
    box-shadow: none;
    color: #fff;
    }

    .custom-btn {
    border: 1px solid #fff;
    color: #fff;
    transition: all 0.3s ease;
    }
    .custom-btn:hover {
    background-color: #fff;
    color: #111;
    }

    @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="glass-box col-md-4 text-white">
  <h2 class="fw-semibold text-center mb-2">Welcome</h2>
  <p class="text-center opacity-75 mb-4">Sign in to your account</p>

  <input type="email" id="email" class="form-control mb-3" placeholder="Email address">
  <input type="password" id="password" class="form-control mb-4" placeholder="Password">

  <button onclick="login()" class="btn custom-btn w-100 mb-3">Login</button>

  <p class="text-center small opacity-75">
    Don’t have an account?
    <a href="register.php" class="text-white text-decoration-underline">Register</a>
  </p>
</div>

<script>
function login() {
  fetch('api.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `action=login&email=${email.value}&password=${password.value}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Login successful',
        timer: 1200,
        showConfirmButton: false
      }).then(() => window.location = 'index.php');
    } else {
      Swal.fire('Login failed', data.message, 'error');
    }
  });
}
</script>

</body>
</html>
