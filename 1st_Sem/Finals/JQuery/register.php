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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

function openSwalAdjust(promiseFn) {
    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
    if (scrollBarWidth > 0) $('body').css('padding-right', scrollBarWidth + 'px');
    $('body').css('overflow','hidden');
    return promiseFn().finally(() => { $('body').css({overflow:'', 'padding-right':''}); });
}

$(document).ready(function(){
    $('#registerForm').submit(function(e){
        e.preventDefault();

        const data = {
            username: $.trim($(this).find('input[name="username"]').val()),
            firstname: $.trim($(this).find('input[name="firstname"]').val()),
            lastname: $.trim($(this).find('input[name="lastname"]').val()),
            password: $(this).find('input[name="password"]').val(),
            role: $(this).find('select[name="role"]').val()
        };

        openSwalAdjust(async () => {
            Swal.showLoading();
            try {
                const j = await $.ajax({
                    url: 'api.php?action=register',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    dataType: 'json'
                });
                Swal.close();

                if (j.success) {
                    Swal.fire({icon:'success', title:'Registered', text:j.message})
                        .then(() => location.href='login.php');
                } else {
                    Swal.fire({icon:'error', title:'Error', text:j.message});
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
