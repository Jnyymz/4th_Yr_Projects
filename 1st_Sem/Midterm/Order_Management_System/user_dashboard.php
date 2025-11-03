<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];
$role = $_SESSION['user']['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light p-3">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg rounded mb-4 px-3" style="background-color: transparent;">
  <a class="navbar-brand fw-bold text-dark">User Dashboard</a>
  <div class="ms-auto d-flex align-items-center">
    <span class="me-3 text-dark">👤 <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</nav>

<!-- Main Layout -->
<div class="container-fluid">
  <div class="row">

    <!-- LEFT: Menu (80%) -->
    <div class="col-md-9">
      <h4 class="fw-bold mb-3">MENU</h4>
      <div class="row" id="menuContainer">
        <!-- Menu items will load here -->
      </div>
    </div>

    <!-- RIGHT: Ordered Items (20%) -->
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0">Ordered Items</h6>
        </div>
        <div class="card-body" id="orderList">
          <p class="text-muted text-center">No items ordered yet.</p>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  loadProducts();
});

function loadProducts() {
  fetch('api.php?action=getProducts')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('menuContainer');
      container.innerHTML = '';

      if (data.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">No products available.</p>';
        return;
      }

      data.forEach(p => {
        const card = document.createElement('div');
        card.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
        card.innerHTML = `
          <div class="card h-100 shadow-sm">
            <img src="${p.image}" class="card-img-top" style="height: 150px; object-fit: cover;">
            <div class="card-body">
              <h6 class="card-title mb-1">${p.name}</h6>
              <p class="text-success fw-bold mb-2">₱${p.price}</p>
              <input type="number" min="1" value="1" class="form-control form-control-sm mb-2" id="qty-${p.id}">
              <button class="btn btn-primary btn-sm w-100" onclick="addToOrder(${p.id}, '${p.name}', ${p.price})">
                Add to Order
              </button>
            </div>
          </div>
        `;
        container.appendChild(card);
      });
    });
}

let orderList = [];

function addToOrder(id, name, price) {
  const qtyInput = document.getElementById(`qty-${id}`);
  const qty = parseInt(qtyInput.value) || 1;
  const existing = orderList.find(i => i.id === id);

  if (existing) existing.qty += qty;
  else orderList.push({ id, name, price, qty });

  renderOrder();
}

function renderOrder() {
  const orderDiv = document.getElementById('orderList');
  if (orderList.length === 0) {
    orderDiv.innerHTML = '<p class="text-muted text-center">No items ordered yet.</p>';
    return;
  }

  orderDiv.innerHTML = orderList.map(item => `
    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
      <div>
        <strong>${item.name}</strong><br>
        <small>Qty: ${item.qty}</small>
      </div>
      <span>₱${(item.price * item.qty).toFixed(2)}</span>
    </div>
  `).join('');

  const total = orderList.reduce((sum, i) => sum + i.price * i.qty, 0);
  orderDiv.innerHTML += `
    <div class="mt-3 border-top pt-2 text-end fw-bold">
      Total: ₱${total.toFixed(2)}
    </div>
    <div class="mt-2">
      <label class="form-label mb-1">Payment Amount:</label>
      <input type="number" id="paymentInput" class="form-control form-control-sm mb-2" min="${total}" placeholder="Enter payment">
    </div>
    <button class="btn btn-success btn-sm w-100" onclick="checkout(${<?= $user_id ?>})">Checkout</button>
  `;
}

function checkout(userId) {
  if (orderList.length === 0) {
    Swal.fire('No items', 'Please add items before checking out.', 'warning');
    return;
  }

  const total = orderList.reduce((sum, i) => sum + i.price * i.qty, 0);
  const payment = parseFloat(document.getElementById('paymentInput').value);

  if (isNaN(payment) || payment < total) {
    Swal.fire('Invalid Payment', 'Payment must be at least ₱' + total, 'warning');
    return;
  }

  const change = (payment - total).toFixed(2);

  const formData = new FormData();
  formData.append('action', 'placeOrder');
  formData.append('user_id', userId);
  formData.append('cart', JSON.stringify(orderList));
  formData.append('payment', payment);
  formData.append('total', total);

  fetch('api.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        Swal.fire('Thank you for ordering!', 'Your change is ₱' + change, 'success');
        orderList = [];
        renderOrder();
      } else {
        Swal.fire('Error', data.message || 'Order failed.', 'error');
      }
    });
}
</script>

</body>
</html>
