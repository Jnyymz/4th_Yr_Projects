<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
  header('Location: login.php');
  exit;
}
$admin_id = $_SESSION['user']['id'];
$admin_name = $_SESSION['user']['username'];
$role = $_SESSION['user']['role'];
?>

<!DOCTYPE html>
<html lang="en" x-data="adminApp()" x-init="loadProducts()">
<head>
  <title>Admin Dashboard</title>
  <meta charset="UTF-8">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light p-4">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg rounded mb-4" style="background-color: transparent;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-dark">Admin Dashboard</a>
    <div class="d-flex align-items-center">
      <span class="me-3 text-dark">👤 <?= htmlspecialchars($admin_name) ?> (<?= htmlspecialchars($role) ?>)</span>
      <a href="notification.php" class="btn btn-warning btn-sm me-2">Notifications</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Main Container -->
<div class="container">

  <button class="btn btn-success my-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
    Add Product
  </button>

  <div class="row g-3">

    <!-- PRODUCT LIST -->
    <template x-for="p in products" :key="p.id">
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <img :src="p.image" class="card-img-top" style="height:150px;object-fit:cover">
          <div class="card-body text-center">
            <h5 class="fw-bold" x-text="p.name"></h5>
            <p>₱<span x-text="p.price"></span></p>
          </div>
        </div>
      </div>
    </template>

  </div>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" enctype="multipart/form-data" x-ref="productForm">

      <div class="modal-header">
        <h5 class="modal-title">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <input type="hidden" x-model="adminID" value="<?= $admin_id ?>">

        <label class="form-label">Product Name</label>
        <input type="text" class="form-control mb-2" x-model="name">

        <label class="form-label">Price</label>
        <input type="number" class="form-control mb-2" x-model="price">

        <label class="form-label">Image</label>
        <input type="file" accept="image/*" class="form-control" x-ref="imageInput">

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

        <button type="button" class="btn btn-primary" x-on:click="addProduct()">
          Add
        </button>
      </div>

    </form>
  </div>
</div>


<!-- ALPINEJS LOGIC -->
<script>
function adminApp() {
    return {

        // DATA
        products: [],

        // ADD PRODUCT FORM DATA
        name: "",
        price: "",
        adminID: <?= $admin_id ?>,
        imageFile: null,

        // LOAD PRODUCTS
        async loadProducts() {
            const res = await fetch("api.php?action=getProducts");
            this.products = await res.json();
        },

        // ADD PRODUCT
        async addProduct() {
            this.imageFile = this.$refs.imageInput.files[0];

            if (!this.name || !this.price || !this.imageFile) {
                Swal.fire("Error", "Please fill in all fields.", "error");
                return;
            }

            let formData = new FormData();
            formData.append("name", this.name);
            formData.append("price", this.price);
            formData.append("admin_id", this.adminID);
            formData.append("image", this.imageFile);

            const res = await fetch("api.php?action=addProduct", {
                method: "POST",
                body: formData
            });

            let result;
            try {
                result = await res.json();
            } catch (e) {
                Swal.fire("Error", "Invalid server response.", "error");
                return;
            }

            if (result.status === "success") {
                Swal.fire("Success!", "Product added successfully.", "success");

                // reset form
                this.$refs.productForm.reset();
                this.name = "";
                this.price = "";
                this.$refs.imageInput.value = "";

                // hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                modal.hide();

                this.loadProducts();
            } else {
                Swal.fire("Error", result.message, "error");
            }
        }
    }
}
</script>

</body>
</html>
