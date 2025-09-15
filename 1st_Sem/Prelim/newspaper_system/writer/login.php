<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">

    <title>Admin Login</title>
  </head>
  <body id="login-body">
    <div id="login-container">
        <div class="card login-card shadow">
            <div class="login-header">
            <h2>Welcome to the Admin Panel</h2>
            <p>Login to continue</p>
            </div>
            <form class="login-form" action="core/handleForms.php" method="POST">
                <div class="card-body">
                    <?php  
                    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
                        if ($_SESSION['status'] == "200") {
                        echo "<p class='success-msg'>{$_SESSION['message']}</p>";
                        } else {
                        echo "<p class='error-msg'>{$_SESSION['message']}</p>"; 
                        }
                    }
                    unset($_SESSION['message']);
                    unset($_SESSION['status']);
                    ?>
                    <div class="form-group">
                        <label for="login-email">Username</label>
                        <input type="email" id="login-email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" class="form-control" name="password" required>
                    </div>
                    <input type="submit" id="login-btn" class="btn btn-primary float-right" name="loginUserBtn" value="Login">
                    
                    <!-- Extra links -->
                    <div class="login-footer">
                        <a href="register.php">Register</a> | 
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

  </body>
</html>
