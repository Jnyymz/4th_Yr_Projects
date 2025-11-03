<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$role = $_SESSION['user']['role'];
$backPage = match ($role) {
  'admin' => 'admin_dashboard.php',
  'superadmin' => 'superadmin_dashboard.php',
  default => 'user_dashboard.php'
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Hide everything except table when printing */
    @media print {
      body * {
        visibility: hidden;
      }
      #reportTable, #reportTable * {
        visibility: visible;
      }
      #reportTable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      thead { display: table-header-group; }
      tfoot { display: table-row-group; }
    }
  </style>
</head>
<body class="bg-light p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Reports</h3>
      <div>
        <button onclick="window.history.back()" class="btn btn-secondary btn-sm me-2">⬅ Back</button>
        <button onclick="printReport()" class="btn btn-primary btn-sm">🖨 Print</button>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="start">Start Date:</label>
        <input type="date" id="start" class="form-control">
      </div>
      <div class="col-md-4">
        <label for="end">End Date:</label>
        <input type="date" id="end" class="form-control">
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button id="filterBtn" class="btn btn-success w-100">Filter</button>
      </div>
    </div>

    <table class="table table-bordered table-striped" id="reportTable">
      <thead class="table-dark">
        <tr>
          <th>Order ID</th>
          <th>User</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Change</th>
          <th>Date Ordered</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr class="fw-bold">
          <td colspan="2" class="text-end">Total Sum:</td>
          <td id="totalSum" colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <script>
    const tbody = document.querySelector('#reportTable tbody');
    const totalSum = document.getElementById('totalSum');
    const filterBtn = document.getElementById('filterBtn');

    function loadReports() {
      const start = document.getElementById('start').value;
      const end = document.getElementById('end').value;

      fetch(`api.php?action=getReports&start=${start}&end=${end}`)
        .then(res => res.json())
        .then(data => {
          let total = 0;
          tbody.innerHTML = data.map(r => {
            total += parseFloat(r.total);
            return `
              <tr>
                <td>${r.id}</td>
                <td>${r.user}</td>
                <td>₱${r.total}</td>
                <td>₱${r.payment}</td>
                <td>₱${r.change_amount}</td>
                <td>${r.date_ordered}</td>
              </tr>
            `;
          }).join('');
          totalSum.textContent = `₱${total.toFixed(2)}`;
        });
    }

    filterBtn.addEventListener('click', loadReports);
    loadReports();

    function printReport() {
      window.print();
    }
  </script>
</body>
</html>
