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

  <!-- AlpineJS -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    @media print {
      body * { visibility: hidden; }
      #reportTable, #reportTable * { visibility: visible; }
      #reportTable {
        position: absolute;
        left: 0; top: 0;
        width: 100%;
      }
      thead { display: table-header-group; }
      tfoot { display: table-row-group; }
    }
  </style>
</head>

<body class="bg-light p-4"
      x-data="reportApp()"
      x-init="loadReports()">

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Reports</h3>
    <div>
      <a href="<?= $backPage ?>" class="btn btn-secondary btn-sm me-2">⬅ Back</a>
      <button class="btn btn-primary btn-sm" @click="printPage()">🖨 Print</button>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <label>Start Date:</label>
      <input type="date" class="form-control" x-model="start">
    </div>
    <div class="col-md-4">
      <label>End Date:</label>
      <input type="date" class="form-control" x-model="end">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button class="btn btn-success w-100" @click="loadReports()">Filter</button>
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

    <tbody>
      <template x-for="r in reports" :key="r.id">
        <tr>
          <td x-text="r.id"></td>
          <td x-text="r.user"></td>
          <td x-text="'₱' + r.total"></td>
          <td x-text="'₱' + r.payment"></td>
          <td x-text="'₱' + r.change_amount"></td>
          <td x-text="r.date_ordered"></td>
        </tr>
      </template>
    </tbody>

    <tfoot>
      <tr class="fw-bold">
        <td colspan="2" class="text-end">Total Sum:</td>
        <td colspan="4" x-text="'₱' + totalSum"></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
function reportApp() {
  return {
    start: '',
    end: '',
    reports: [],
    totalSum: '0.00',

    async loadReports() {
      try {
        const url = `api.php?action=getReports&start=${this.start}&end=${this.end}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error("Network error");

        const data = await res.json();

        this.reports = data;
        this.calculateTotal();
      }
      catch (e) {
        Swal.fire('Error', 'Failed to load reports', 'error');
      }
    },

    calculateTotal() {
      let sum = 0;
      for (const r of this.reports) {
        sum += parseFloat(r.total);
      }
      this.totalSum = sum.toFixed(2);
    },

    printPage() {
      window.print();
    }
  }
}
</script>

</body>
</html>
