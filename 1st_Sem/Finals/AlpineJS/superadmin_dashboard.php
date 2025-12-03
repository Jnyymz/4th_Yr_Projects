<?php
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
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-white"
      x-data="superadminApp()"
      x-init="loadAccounts()">

<nav class="navbar navbar-expand-lg" style="background-color: transparent;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark" href="#">Super Admin Dashboard</a>
        <div class="d-flex align-items-center ms-auto">
            <a href="notification.php" class="btn btn-outline-dark btn-sm me-2">Notifications</a>
            <a href="reports.php" class="btn btn-outline-dark btn-sm me-3">Reports</a>
            <span class="text-dark me-3">
                Hello, <strong><?= htmlspecialchars($user['firstname']) ?></strong>
            </span>
            <button class="btn btn-danger btn-sm" @click="logout()">Logout</button>
        </div>
    </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Accounts (Users & Admins)</h4>
    <div>
      <button class="btn btn-primary" @click="addAdmin()">Add Admin</button>
      <button class="btn btn-secondary ms-2" @click="loadAccounts()">Refresh</button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Name</th><th>Role</th><th>Created At</th><th>Status</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <template x-for="acc in accounts" :key="acc.id">
            <tr>
                <td x-text="acc.id"></td>
                <td x-text="acc.username"></td>
                <td x-text="acc.firstname + ' ' + acc.lastname"></td>
                <td x-text="acc.role"></td>
                <td x-text="acc.created_at"></td>
                <td>
                    <span class="badge"
                          :class="acc.is_suspended == 1 ? 'bg-danger' : 'bg-success'"
                          x-text="acc.is_suspended == 1 ? 'Suspended' : 'Active'">
                    </span>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm"
                            @click="toggleSuspend(acc.id, acc.is_suspended)">
                        <span x-text="acc.is_suspended == 1 ? 'Unsuspend' : 'Suspend'"></span>
                    </button>
                </td>
            </tr>
        </template>
      </tbody>
    </table>
  </div>
</div>

<script>
function superadminApp() {
    return {
        accounts: [],

        async loadAccounts() {
            try {
                Swal.showLoading();
                const res = await fetch("api.php?action=get_accounts");
                const data = await res.json();
                Swal.close();

                if (!data.success) {
                    return Swal.fire({ icon: 'error', text: data.message });
                }

                this.accounts = data.accounts;
            } catch (e) {
                Swal.close();
                Swal.fire({ icon:'error', text:'Failed to load accounts.' });
            }
        },

        async addAdmin() {
            const { value: form } = await Swal.fire({
                title: 'Add Admin',
                html: `
                    <input id="sa_username" class="swal2-input" placeholder="Username">
                    <input id="sa_firstname" class="swal2-input" placeholder="First name">
                    <input id="sa_lastname" class="swal2-input" placeholder="Last name">
                    <input id="sa_password" type="password" class="swal2-input" placeholder="Password">
                `,
                focusConfirm: false,
                showCancelButton: true,
                preConfirm: () => ({
                    username: document.getElementById("sa_username").value.trim(),
                    firstname: document.getElementById("sa_firstname").value.trim(),
                    lastname: document.getElementById("sa_lastname").value.trim(),
                    password: document.getElementById("sa_password").value
                })
            });

            if (!form) return;

            Swal.showLoading();
            const res = await fetch("api.php?action=add_admin", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(form)
            });
            const data = await res.json();
            Swal.close();

            if (data.success) {
                Swal.fire({icon: 'success', text: data.message});
                this.loadAccounts();
            } else {
                Swal.fire({icon: 'error', text: data.message});
            }
        },

        async toggleSuspend(id, currentState) {
            const ask = await Swal.fire({
                title: "Are you sure?",
                text: "This will toggle suspension for the account.",
                icon: "warning",
                showCancelButton: true
            });

            if (!ask.isConfirmed) return;

            Swal.showLoading();

            const res = await fetch("api.php?action=toggle_suspend", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({id})
            });
            const data = await res.json();
            Swal.close();

            if (data.success) {
                Swal.fire({icon:'success', text:data.message});
                this.loadAccounts();
            } else {
                Swal.fire({icon:'error', text:data.message});
            }
        },

        async logout() {
            Swal.showLoading();
            const res = await fetch("api.php?action=logout");
            const data = await res.json();
            Swal.close();

            if (data.success) {
                location.href = "login.php";
            }
        }
    };
}
</script>

</body>
</html>
