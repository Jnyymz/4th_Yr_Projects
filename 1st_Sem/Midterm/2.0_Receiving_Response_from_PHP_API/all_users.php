<?php
require 'config.php';
if (empty($_SESSION['user'])) {
    header('Location: login.php'); exit;
}
if (!($_SESSION['user']['is_admin'] ?? false)) {
    header('Location: index.php'); exit;
}
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>All Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/core-js-bundle@3.34.0/minified.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<nav class="navbar navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="index.php">MyApp</a>
    <div class="ms-auto">
      <span class="me-3">Admin: <?= htmlspecialchars($user['username']) ?></span>
      <button id="logoutBtn" class="btn btn-outline-danger btn-sm">Logout</button>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="w-50">
      <input id="searchInput" class="form-control" placeholder="Search users (username, firstname, lastname)">
    </div>
    <div>
      <button id="addUserBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add user</button>
    </div>
  </div>

  <div class="table-responsive">
    <table id="usersTable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>First name</th>
          <th>Last name</th>
          <th>Is Admin</th>
          <th>Date Added</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- Add User Modal (Bootstrap 5) -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addUserForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add user</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Username</label>
          <input id="modal_username" name="username" class="form-control" required>
          <div id="modal_username_feedback" class="form-text"></div>
        </div>
        <div class="mb-2">
          <label class="form-label">First name</label>
          <input id="modal_firstname" name="firstname" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Last name</label>
          <input id="modal_lastname" name="lastname" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <input id="modal_password" name="password" type="password" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Confirm Password</label>
          <input id="modal_confirm_password" name="confirm_password" type="password" class="form-control" required>
        </div>
        <div class="form-check">
          <input id="modal_is_admin" name="is_admin" class="form-check-input" type="checkbox">
          <label class="form-check-label">Is Admin</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" type="submit">Add user</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const getUsers = async () => {
  const res = await fetch('api.php?action=get_users');
  const data = await res.json();
  if (!data.success) {
    Swal.fire('Error', data.message || 'Could not fetch users', 'error');
    return;
  }
  const tbody = document.querySelector('#usersTable tbody');
  tbody.innerHTML = '';
  data.users.forEach(u => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${u.id}</td>
      <td>${escapeHtml(u.username)}</td>
      <td>${escapeHtml(u.firstname)}</td>
      <td>${escapeHtml(u.lastname)}</td>
      <td>${u.is_admin ? 'Yes' : 'No'}</td>
      <td>${u.date_added}</td>
    `;
    tbody.appendChild(tr);
  });
};

function escapeHtml(s = '') {
  return s.replaceAll && typeof s === 'string'
    ? s.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;')
    : s;
}

document.getElementById('logoutBtn').addEventListener('click', async () => {
  await fetch('api.php?action=logout');
  window.location.href = 'login.php';
});

document.addEventListener('DOMContentLoaded', () => {
  getUsers();
});

document.getElementById('searchInput').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#usersTable tbody tr').forEach(tr => {
    const txt = tr.innerText.toLowerCase();
    tr.style.display = txt.includes(q) ? '' : 'none';
  });
});

// check username as the user types inside modal
let usernameTimer = null;
document.getElementById('modal_username').addEventListener('input', function() {
  const el = this;
  const fb = document.getElementById('modal_username_feedback');
  const name = el.value.trim();
  fb.textContent = '';
  if (usernameTimer) clearTimeout(usernameTimer);
  if (!name) return;
  usernameTimer = setTimeout(async () => {
    const res = await fetch(`api.php?action=check_username&username=${encodeURIComponent(name)}`);
    const data = await res.json();
    if (data.available) {
      fb.textContent = 'Username available';
    } else {
      fb.textContent = 'Username taken';
    }
  }, 400);
});

document.getElementById('addUserForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('modal_username').value.trim();
  const firstname = document.getElementById('modal_firstname').value.trim();
  const lastname = document.getElementById('modal_lastname').value.trim();
  const password = document.getElementById('modal_password').value;
  const confirm = document.getElementById('modal_confirm_password').value;
  const is_admin = document.getElementById('modal_is_admin').checked ? 1 : 0;

  if (!username || !firstname || !lastname || !password || !confirm) {
    Swal.fire('Missing fields','Please fill all fields','warning'); return;
  }
  if (password.length < 8) {
    Swal.fire('Weak password','Password must be at least 8 characters','warning'); return;
  }
  if (password !== confirm) {
    Swal.fire('Mismatch','Passwords do not match','warning'); return;
  }

  // send add_user via API
  const res = await fetch('api.php?action=add_user', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({username, firstname, lastname, password, confirm_password: confirm, is_admin})
  });
  const data = await res.json();
  if (data.success) {
    Swal.fire('Added','User added successfully','success');
    var modalEl = document.getElementById('addUserModal');
    var bsModal = bootstrap.Modal.getInstance(modalEl);
    bsModal.hide();
    getUsers();
    document.getElementById('addUserForm').reset();
  } else {
    Swal.fire('Error', data.message || 'Failed to add user', 'error');
  }
});
</script>
</body>
</html>
