<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">

  <title>Register as Writer</title>
</head>
<body id="register-body">
  <div id="register-container">
    <div class="register-card">
      <h2>Welcome to the Writer Side</h2>
      <p>Register now as a writer!</p>
      <form action="core/handleForms.php" method="POST" class="register-form">
        <div class="form-group">
          <label>Username</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" class="form-control" name="confirm_password" required>
        </div>
        <button type="submit" id="register-btn" name="insertNewUserBtn">Register</button>
      </form>
      <div class="register-footer">
        <p>Already have an account? <a href="login.php">Go to Login</a></p>
      </div>
    </div>
  </div>
</body>
</html>