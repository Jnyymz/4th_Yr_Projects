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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  <button class="btn btn-success my-3" id="addProductBtn">Add Product</button>
  <div id="productList" class="row g-3"></div>
</div>

<script>
const admin_id = <?= $admin_id ?>;

// Load Products
async function loadProducts(){
  const res = await fetch('api.php?action=getProducts');
  const data = await res.json();
  document.getElementById('productList').innerHTML = data.map(p => `
    <div class="col-md-3">
      <div class="card h-100 shadow-sm">
        <img src="${p.image}" class="card-img-top" style="height:150px;object-fit:cover">
        <div class="card-body text-center">
          <h5 class="fw-bold">${p.name}</h5>
          <p>₱${p.price}</p>
        </div>
      </div>
    </div>
  `).join('');
}
loadProducts();

// Add Product (with file upload)
document.getElementById("addProductBtn").addEventListener("click", async () => {
  const { value: confirmed } = await Swal.fire({
    title: "Add Product",
    html: `
      <input id="pname" class="swal2-input" placeholder="Product name">
      <input id="pprice" type="number" class="swal2-input" placeholder="Price">
      <input id="pimage" type="file" accept="image/*" class="form-control mt-2">
    `,
    showCancelButton: true,
    confirmButtonText: "Add",
    focusConfirm: false,
    preConfirm: () => {
      const name = document.getElementById("pname").value.trim();
      const price = document.getElementById("pprice").value.trim();
      const file = document.getElementById("pimage").files[0];
      if (!name || !price || !file) {
        Swal.showValidationMessage("Please complete all fields.");
        return false;
      }
      return { name, price, file };
    }
  });

  if (confirmed) {
    const fd = new FormData();
    fd.append("name", confirmed.name);
    fd.append("price", confirmed.price);
    fd.append("image", confirmed.file);
    fd.append("admin_id", admin_id);

    try {
      const res = await fetch("api.php?action=addProduct", { method: "POST", body: fd });
      const data = await res.json();
      if (data.status === "success") {
        Swal.fire("Added!", "Product successfully added.", "success");
        loadProducts();
      } else {
        Swal.fire("Error!", data.message || "Failed to add product.", "error");
      }
    } catch (err) {
      Swal.fire("Error!", "Request failed.", "error");
      console.error(err);
    }
  }
});


</script>
</body>
</html>
