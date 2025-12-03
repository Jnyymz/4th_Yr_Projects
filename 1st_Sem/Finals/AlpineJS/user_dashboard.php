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
<html lang="en" x-data="menuApp()" x-init="loadProducts()">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light p-3">

<nav class="navbar navbar-expand-lg rounded mb-4 px-3" style="background-color: transparent;">
  <a class="navbar-brand fw-bold text-dark">User Dashboard</a>
  <div class="ms-auto d-flex align-items-center">
    <span class="me-3 text-dark">👤 <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">

    <!-- LEFT MENU -->
    <div class="col-md-9">
      <h4 class="fw-bold mb-3">MENU</h4>
      <div class="row">
        <!-- PRODUCTS -->
        <template x-for="p in products" :key="p.id">
          <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm">
              <img :src="p.image" class="card-img-top" style="height:150px; object-fit:cover;">
              <div class="card-body">
                <h6 class="card-title mb-1" x-text="p.name"></h6>
                <p class="text-success fw-bold mb-2">₱<span x-text="p.price"></span></p>

                <input type="number" min="1" class="form-control form-control-sm mb-2"
                       x-model.number="p.qty">

                <button class="btn btn-primary btn-sm w-100"
                        x-on:click="addToOrder(p)">
                  Add to Order
                </button>
              </div>
            </div>
          </div>
        </template>

        <p x-show="products.length === 0" class="text-center text-muted">
          No products available.
        </p>
      </div>
    </div>

    <!-- RIGHT ORDER LIST -->
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0">Ordered Items</h6>
        </div>

        <div class="card-body">

          <template x-if="order.length === 0">
            <p class="text-muted text-center">No items ordered yet.</p>
          </template>

          <template x-for="item in order" :key="item.id">
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
              <div>
                <strong x-text="item.name"></strong><br>
                <small>Qty: <span x-text="item.qty"></span></small>
              </div>
              <span>₱<span x-text="(item.price * item.qty).toFixed(2)"></span></span>
            </div>
          </template>

          <!-- TOTAL + PAYMENT -->
          <div class="mt-3 border-top pt-2 text-end fw-bold" x-show="order.length > 0">
            Total: ₱<span x-text="total"></span>
          </div>

          <div x-show="order.length > 0" class="mt-2">
            <label class="form-label mb-1">Payment Amount:</label>
            <input type="number" class="form-control form-control-sm mb-2"
                   x-model.number="payment" :min="total">
          </div>

          <button class="btn btn-success btn-sm w-100"
                  x-show="order.length > 0"
                  x-on:click="checkout(<?= $user_id ?>)">
            Checkout
          </button>

        </div>
      </div>
    </div>

  </div>
</div>


<!-- ALPINEJS APP LOGIC -->
<script>
function menuApp() {
    return {

        products: [],
        order: [],
        payment: 0,

        get total() {
            return this.order.reduce((sum, i) => sum + i.price * i.qty, 0).toFixed(2);
        },

        async loadProducts() {
            const res = await fetch('api.php?action=getProducts');
            const data = await res.json();

            this.products = data.map(p => ({ ...p, qty: 1 }));
        },

        addToOrder(product) {
            const existing = this.order.find(i => i.id === product.id);

            if (existing) {
                existing.qty += product.qty;
            } else {
                this.order.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    qty: product.qty
                });
            }

            product.qty = 1; // reset field
        },

        async checkout(userId) {
            if (this.order.length === 0) {
                Swal.fire('No items', 'Please add items before checking out.', 'warning');
                return;
            }

            const total = parseFloat(this.total);
            if (!this.payment || this.payment < total) {
                Swal.fire('Invalid Payment', 'Payment must be at least ₱' + total, 'warning');
                return;
            }

            const change = (this.payment - total).toFixed(2);

            let formData = new FormData();
            formData.append('action', 'placeOrder');
            formData.append('user_id', userId);
            formData.append('cart', JSON.stringify(this.order));
            formData.append('payment', this.payment);
            formData.append('total', total);

            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire('Thank you for ordering!', 'Your change is ₱' + change, 'success');
                this.order = [];
                this.payment = 0;
            } else {
                Swal.fire('Error', result.message || 'Order failed.', 'error');
            }
        }

    };
}
</script>

</body>
</html>
