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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card p-4">
          <h3 class="mb-3">Login</h3>
          <form id="loginForm">
            <div class="mb-2">
              <label>Username</label>
              <input class="form-control" name="username" required>
            </div>
            <div class="mb-2">
              <label>Password</label>
              <input type="password" class="form-control" name="password" required>
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
    if (scrollBarWidth > 0) $('body').css('padding-right', scrollBarWidth + 'px');
    $('body').css('overflow','hidden');
    return promiseFn().finally(() => { $('body').css({overflow:'', 'padding-right':''}); });
}

$(document).ready(function(){
    $('#loginForm').submit(function(e){
        e.preventDefault();

        const username = $.trim($(this).find('input[name="username"]').val());
        const password = $(this).find('input[name="password"]').val();
        const data = { username, password };

        openSwalAdjust(async () => {
            Swal.showLoading();
            try {
                const j = await $.ajax({
                    url: 'api.php?action=login',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    dataType: 'json'
                });

                Swal.close();

                if (j.success) {
                    Swal.fire({icon:'success', title:'Welcome', text:'Login successful'})
                        .then(() => {
                            if (j.user.role === 'superadmin') location.href = 'superadmin_dashboard.php';
                            else if (j.user.role === 'admin') location.href = 'admin_dashboard.php';
                            else location.href = 'user_dashboard.php';
                        });
                } else {
                    Swal.fire({icon:'error', title:'Login failed', text:j.message});
                }
            } catch (err) {
                Swal.close();
                Swal.fire({icon:'error', title:'Error', text:err.responseText || err.statusText || err.message});
            }
        });
    });
});
</script>
</body>
</html>
