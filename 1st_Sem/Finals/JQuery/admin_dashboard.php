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
<html lang="en">
<head>
  <title>Admin Dashboard</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  <button class="btn btn-success my-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
  <div id="productList" class="row g-3"></div>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="productForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="adminID" value="<?= $admin_id ?>">

        <label class="form-label">Product Name</label>
        <input type="text" id="productName" class="form-control mb-2">

        <label class="form-label">Price</label>
        <input type="number" id="productPrice" class="form-control mb-2">

        <label class="form-label">Image</label>
        <input type="file" id="productImage" accept="image/*" class="form-control">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="addProductBtn" class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</div>

<script>
const admin_id = <?= $admin_id ?>;

// LOAD PRODUCTS
function loadProducts() {
  $.ajax({
    url: "api.php?action=getProducts",
    method: "GET",
    dataType: "json",
    success: function(data) {
      let html = "";
      data.forEach(p => {
        html += `
          <div class="col-md-3">
            <div class="card h-100 shadow-sm">
              <img src="${p.image}" class="card-img-top" style="height:150px;object-fit:cover">
              <div class="card-body text-center">
                <h5 class="fw-bold">${p.name}</h5>
                <p>₱${p.price}</p>
              </div>
            </div>
          </div>
        `;
      });
      $("#productList").html(html);
    }
  });
}

loadProducts();

// ADD PRODUCT FUNCTION

function addProduct() {
    const name = $("#productName").val().trim();
    const price = $("#productPrice").val().trim();
    const adminID = $("#adminID").val();
    const image = $("#productImage")[0].files[0];

    if (!name || !price || !image) {
        Swal.fire("Error", "Please fill in all fields.", "error");
        return;
    }

    let formData = new FormData();
    formData.append("name", name);
    formData.append("price", price);
    formData.append("admin_id", adminID);
    formData.append("image", image);

    $.ajax({
        url: "api.php?action=addProduct",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

            let res = {};
            try { res = JSON.parse(response); }
            catch (e) {
                Swal.fire("Error", "Invalid server response.", "error");
                return;
            }

            if (res.status === "success") {
                Swal.fire("Success!", "Product added successfully.", "success");
                $("#productForm")[0].reset();
                $("#addProductModal").modal("hide");
                loadProducts();
            } else {
                Swal.fire("Error", res.message, "error");
            }
        },
        error: function(err) {
            Swal.fire("Error", "Request failed.", "error");
        }
    });
}

// CLICK EVENT
$(document).on("click", "#addProductBtn", addProduct);
</script>

</body>
</html>
