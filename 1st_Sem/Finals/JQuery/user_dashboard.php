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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
$(document).ready(function() {
    loadProducts();
});

function loadProducts() {
    $.getJSON('api.php?action=getProducts', function(data) {
        const container = $('#menuContainer');
        container.empty();

        if (data.length === 0) {
            container.html('<p class="text-center text-muted">No products available.</p>');
            return;
        }

        $.each(data, function(_, p) {
            const card = $(`
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="${p.image}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title mb-1">${p.name}</h6>
                            <p class="text-success fw-bold mb-2">₱${p.price}</p>
                            <input type="number" min="1" value="1" class="form-control form-control-sm mb-2" id="qty-${p.id}">
                            <button class="btn btn-primary btn-sm w-100 add-to-order" data-id="${p.id}" data-name="${p.name}" data-price="${p.price}">
                                Add to Order
                            </button>
                        </div>
                    </div>
                </div>
            `);
            container.append(card);
        });

        // Bind add-to-order button
        $('.add-to-order').click(function() {
            const id = parseInt($(this).data('id'));
            const name = $(this).data('name');
            const price = parseFloat($(this).data('price'));
            addToOrder(id, name, price);
        });
    });
}

let orderList = [];

function addToOrder(id, name, price) {
    const qty = parseInt($(`#qty-${id}`).val()) || 1;
    const existing = orderList.find(i => i.id === id);

    if (existing) existing.qty += qty;
    else orderList.push({ id, name, price, qty });

    renderOrder();
}

function renderOrder() {
    const orderDiv = $('#orderList');
    if (orderList.length === 0) {
        orderDiv.html('<p class="text-muted text-center">No items ordered yet.</p>');
        return;
    }

    let html = '';
    $.each(orderList, function(_, item) {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>Qty: ${item.qty}</small>
                </div>
                <span>₱${(item.price * item.qty).toFixed(2)}</span>
            </div>
        `;
    });

    const total = orderList.reduce((sum, i) => sum + i.price * i.qty, 0);

    html += `
        <div class="mt-3 border-top pt-2 text-end fw-bold">
            Total: ₱${total.toFixed(2)}
        </div>
        <div class="mt-2">
            <label class="form-label mb-1">Payment Amount:</label>
            <input type="number" id="paymentInput" class="form-control form-control-sm mb-2" min="${total}" placeholder="Enter payment">
        </div>
        <button class="btn btn-success btn-sm w-100" id="checkoutBtn">Checkout</button>
    `;

    orderDiv.html(html);

    $('#checkoutBtn').off('click').on('click', function() {
        checkout(<?= $user_id ?>);
    });
}

function checkout(userId) {
    if (orderList.length === 0) {
        Swal.fire('No items', 'Please add items before checking out.', 'warning');
        return;
    }

    const total = orderList.reduce((sum, i) => sum + i.price * i.qty, 0);
    const payment = parseFloat($('#paymentInput').val());

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

    $.ajax({
        url: 'api.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.status === 'success') {
                Swal.fire('Thank you for ordering!', 'Your change is ₱' + change, 'success');
                orderList = [];
                renderOrder();
            } else {
                Swal.fire('Error', data.message || 'Order failed.', 'error');
            }
        },
        dataType: 'json'
    });
}
</script>

</body>
</html>
