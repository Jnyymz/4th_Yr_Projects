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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      <button id="backBtn" class="btn btn-secondary btn-sm me-2">⬅ Back</button>
      <button id="printBtn" class="btn btn-primary btn-sm">🖨 Print</button>
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
$(document).ready(function(){

  function loadReports() {
    const start = $('#start').val();
    const end = $('#end').val();

    $.getJSON(`api.php?action=getReports&start=${start}&end=${end}`, function(data){
      let total = 0;
      const tbody = $('#reportTable tbody');
      tbody.empty();

      $.each(data, function(_, r){
        total += parseFloat(r.total);
        tbody.append(`
          <tr>
            <td>${r.id}</td>
            <td>${r.user}</td>
            <td>₱${r.total}</td>
            <td>₱${r.payment}</td>
            <td>₱${r.change_amount}</td>
            <td>${r.date_ordered}</td>
          </tr>
        `);
      });

      $('#totalSum').text(`₱${total.toFixed(2)}`);
    }).fail(function(){
      Swal.fire('Error', 'Failed to load reports', 'error');
    });
  }

  $('#filterBtn').click(loadReports);

  $('#backBtn').click(function(){
    window.history.back();
  });

  $('#printBtn').click(function(){
    window.print();
  });

  // Initial load
  loadReports();

});
</script>
</body>
</html>
