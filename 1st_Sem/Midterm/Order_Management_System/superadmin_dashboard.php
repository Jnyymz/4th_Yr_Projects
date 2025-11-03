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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-white">
    <nav class="navbar navbar-expand-lg" style="background-color: transparent;">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand fw-bold text-dark" href="#">Super Admin Dashboard</a>

            <!-- Right side (User info and buttons) -->
            <div class="d-flex align-items-center ms-auto">
                <a href="notification.php" class="btn btn-outline-dark btn-sm me-2">
                    Notifications
                </a>
                <a href="reports.php" class="btn btn-outline-dark btn-sm me-3">
                    Reports
                </a>
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
  if (scrollBarWidth > 0) document.body.style.paddingRight = scrollBarWidth + 'px';
  document.body.style.overflow = 'hidden';
}
function restoreBodyAfterSwal() {
  document.body.style.overflow = '';
  document.body.style.paddingRight = '';
}

// wrapper to handle body shift
async function withSwal(promiseFn) {
  adjustBodyForSwalOpen();
  try { return await promiseFn(); }
  finally { restoreBodyAfterSwal(); }
}

async function fetchAccounts() {
  const res = await fetch('api.php?action=get_accounts');
  return res.json();
}

async function loadAccounts() {
  Swal.showLoading();
  const j = await fetchAccounts();
  Swal.close();
  if (!j.success) return Swal.fire({icon:'error',text:j.message});
  const tbody = document.querySelector('#accountsTable tbody');
  tbody.innerHTML = '';
  j.accounts.forEach(a=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${a.id}</td>
      <td>${a.username}</td>
      <td>${a.firstname} ${a.lastname}</td>
      <td>${a.role}</td>
      <td>${a.created_at}</td>
      <td>${a.is_suspended==1 ? '<span class="badge bg-danger">Suspended</span>' : '<span class="badge bg-success">Active</span>'}</td>
      <td>
        <button class="btn btn-sm btn-warning toggleSuspendBtn" data-id="${a.id}">${a.is_suspended==1 ? 'Unsuspend' : 'Suspend'}</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

document.getElementById('refreshBtn').addEventListener('click', loadAccounts);

document.getElementById('addAdminBtn').addEventListener('click', async function(){
  await withSwal(async () => {
    const { value: formValues } = await Swal.fire({
      title: 'Add Admin',
      html:
        '<input id="sa_username" class="swal2-input" placeholder="Username">' +
        '<input id="sa_firstname" class="swal2-input" placeholder="First name">' +
        '<input id="sa_lastname" class="swal2-input" placeholder="Last name">' +
        '<input id="sa_password" type="password" class="swal2-input" placeholder="Password">',
      focusConfirm: false,
      preConfirm: () => {
        return {
          username: document.getElementById('sa_username').value.trim(),
          firstname: document.getElementById('sa_firstname').value.trim(),
          lastname: document.getElementById('sa_lastname').value.trim(),
          password: document.getElementById('sa_password').value
        }
      },
      showCancelButton: true
    });

    if (!formValues) return; // cancelled

    Swal.showLoading();
    const res = await fetch('api.php?action=add_admin', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(formValues)
    });
    const j = await res.json();
    Swal.close();
    if (j.success) {
      Swal.fire({icon:'success',text:j.message});
      loadAccounts();
    } else {
      Swal.fire({icon:'error',text:j.message});
    }
  });
});

document.querySelector('#accountsTable').addEventListener('click', async function(e){
  if (e.target.classList.contains('toggleSuspendBtn')) {
    const id = e.target.dataset.id;
    await withSwal(async () => {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will toggle suspension for the account.",
        icon: 'warning',
        showCancelButton: true
      }).then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.showLoading();
        const res = await fetch('api.php?action=toggle_suspend', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({id: id})
        });
        const j = await res.json();
        Swal.close();
        if (j.success) {
          Swal.fire({icon:'success',text:j.message});
          loadAccounts();
        } else {
          Swal.fire({icon:'error',text:j.message});
        }
      });
    });
  }
});

document.getElementById('logoutBtn').addEventListener('click', async ()=>{
  await withSwal(async ()=>{
    Swal.showLoading();
    const res = await fetch('api.php?action=logout');
    const j = await res.json();
    Swal.close();
    if (j.success) location.href = 'login.php';
  });
});

// initial load
loadAccounts();
</script>
</body>
</html>
