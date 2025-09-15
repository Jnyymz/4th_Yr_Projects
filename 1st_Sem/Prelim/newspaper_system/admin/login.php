<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" 
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" 
          crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">

    <title>Admin Login</title>
  </head>
  <body id="login-body">
    <div id="login-container">
      <div class="login-card">
        <h2>Welcome to the Admin Panel!</h2>
        <p>Please login to continue.</p>

        <form class="login-form" action="core/handleForms.php" method="POST">
          <?php  
            if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
              $color = $_SESSION['status'] == "200" ? "green" : "red";
              echo "<h5 style='color: {$color}; text-align:center;'>{$_SESSION['message']}</h5>";
            }
            unset($_SESSION['message']);
            unset($_SESSION['status']);
          ?>

          <div class="form-group">
            <label for="email">Username</label>
            <input type="email" class="form-control" name="email" id="email" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
          </div>

          <button type="submit" class="btn" id="login-btn" name="loginUserBtn">Login</button>
        </form>

        <div class="login-footer">
          <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
      </div>
    </div>
  </body>
</html>
