<?php require_once 'classloader.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" 
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" 
        crossorigin="anonymous">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">

  <title>Admin Registration</title>
</head>
<body id="register-body">
  <div id="register-container">
    <div class="register-card">
      <h2>Register as Admin</h2>
      <p>Create your admin account to access the dashboard</p>

      <?php  
        if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
          $color = $_SESSION['status'] == "200" ? "green" : "red";
          echo "<h5 style='color: {$color}; text-align:center;'>{$_SESSION['message']}</h5>";
        }
        unset($_SESSION['message']);
        unset($_SESSION['status']);
      ?>

      <form class="register-form" action="core/handleForms.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" name="insertNewUserBtn" id="register-btn">Register</button>
      </form>

      <div class="register-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>
</body>
</html>
