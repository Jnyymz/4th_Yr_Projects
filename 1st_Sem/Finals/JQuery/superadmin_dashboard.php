<?php
// superadmin_dashboard.php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Super Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-white">
<nav class="navbar navbar-expand-lg" style="background-color: transparent;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark" href="#">Super Admin Dashboard</a>
        <div class="d-flex align-items-center ms-auto">
            <a href="notification.php" class="btn btn-outline-dark btn-sm me-2">Notifications</a>
            <a href="reports.php" class="btn btn-outline-dark btn-sm me-3">Reports</a>
            <span class="text-dark me-3">
                Hello, <strong><?= htmlspecialchars($user['firstname']) ?></strong>
            </span>
            <button id="logoutBtn" class="btn btn-danger btn-sm">Logout</button>
        </div>
    </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Accounts (Users & Admins)</h4>
    <div>
      <button id="addAdminBtn" class="btn btn-primary">Add Admin</button>
      <button id="refreshBtn" class="btn btn-secondary ms-2">Refresh</button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered" id="accountsTable">
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Name</th><th>Role</th><th>Created At</th><th>Status</th><th>Action</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
function adjustBodyForSwalOpen() {
    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
    if (scrollBarWidth > 0) $('body').css('padding-right', scrollBarWidth + 'px');
    $('body').css('overflow', 'hidden');
}

function restoreBodyAfterSwal() {
    $('body').css({overflow:'', 'padding-right':''});
}

async function withSwal(promiseFn) {
    adjustBodyForSwalOpen();
    try { return await promiseFn(); }
    finally { restoreBodyAfterSwal(); }
}

function fetchAccounts() {
    return $.getJSON('api.php?action=get_accounts');
}

async function loadAccounts() {
    Swal.showLoading();
    try {
        const j = await fetchAccounts();
        Swal.close();
        if (!j.success) return Swal.fire({icon:'error', text:j.message});
        const tbody = $('#accountsTable tbody');
        tbody.empty();

        $.each(j.accounts, function(_, a){
            const statusBadge = a.is_suspended==1 
                ? '<span class="badge bg-danger">Suspended</span>' 
                : '<span class="badge bg-success">Active</span>';
            const actionBtn = `<button class="btn btn-sm btn-warning toggleSuspendBtn" data-id="${a.id}">
                                 ${a.is_suspended==1 ? 'Unsuspend' : 'Suspend'}
                               </button>`;
            tbody.append(`
                <tr>
                    <td>${a.id}</td>
                    <td>${a.username}</td>
                    <td>${a.firstname} ${a.lastname}</td>
                    <td>${a.role}</td>
                    <td>${a.created_at}</td>
                    <td>${statusBadge}</td>
                    <td>${actionBtn}</td>
                </tr>
            `);
        });
    } catch(err) {
        Swal.close();
        Swal.fire({icon:'error', text:'Failed to load accounts.'});
    }
}

// Refresh button
$('#refreshBtn').click(loadAccounts);

// Add Admin
$('#addAdminBtn').click(async function(){
    await withSwal(async () => {
        const { value: formValues } = await Swal.fire({
            title: 'Add Admin',
            html:
                '<input id="sa_username" class="swal2-input" placeholder="Username">' +
                '<input id="sa_firstname" class="swal2-input" placeholder="First name">' +
                '<input id="sa_lastname" class="swal2-input" placeholder="Last name">' +
                '<input id="sa_password" type="password" class="swal2-input" placeholder="Password">',
            focusConfirm: false,
            preConfirm: () => ({
                username: $('#sa_username').val().trim(),
                firstname: $('#sa_firstname').val().trim(),
                lastname: $('#sa_lastname').val().trim(),
                password: $('#sa_password').val()
            }),
            showCancelButton: true
        });

        if (!formValues) return; // cancelled

        Swal.showLoading();
        const j = await $.ajax({
            url: 'api.php?action=add_admin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formValues),
            dataType: 'json'
        });
        Swal.close();
        if (j.success) {
            Swal.fire({icon:'success', text:j.message});
            loadAccounts();
        } else {
            Swal.fire({icon:'error', text:j.message});
        }
    });
});

// Toggle Suspend
$('#accountsTable').on('click', '.toggleSuspendBtn', async function(){
    const id = $(this).data('id');
    await withSwal(async () => {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This will toggle suspension for the account.",
            icon: 'warning',
            showCancelButton: true
        });
        if (!result.isConfirmed) return;

        Swal.showLoading();
        const j = await $.ajax({
            url: 'api.php?action=toggle_suspend',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({id: id}),
            dataType: 'json'
        });
        Swal.close();
        if (j.success) {
            Swal.fire({icon:'success', text:j.message});
            loadAccounts();
        } else {
            Swal.fire({icon:'error', text:j.message});
        }
    });
});

// Logout
$('#logoutBtn').click(async function(){
    await withSwal(async ()=>{
        Swal.showLoading();
        const j = await $.getJSON('api.php?action=logout');
        Swal.close();
        if (j.success) location.href = 'login.php';
    });
});

// Initial load
loadAccounts();
</script>
</body>
</html>
