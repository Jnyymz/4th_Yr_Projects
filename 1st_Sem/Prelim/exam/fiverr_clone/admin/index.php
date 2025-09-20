<?php
session_start();
require_once "classes/User.php";
require_once "classes/Categories.php";
require_once "classes/Proposal.php";

$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$categories = new Categories();
$proposal = new Proposal();

$list = $categories->getCategories();

// Selected category (default = All)


$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$proposals = $selectedCategory > 0
    ? $proposal->getProposalsByCategory($selectedCategory)
    : $proposal->getProposalsWithCategory();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Dashboard</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="#">Categories</a>
            </li>
        </ul>

        <!-- Right side -->
        <form class="d-flex" method="post" action="core/handleForms.php">
            <button class="btn btn-outline-light" type="submit" name="logout">Logout</button>
        </form>
        </div>
    </div>
    </nav>


  <!-- Category Add Form -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="post" action="core/handleForms.php" class="row g-2">
        <div class="col-md-8">
          <input type="text" name="category_name" class="form-control" placeholder="New Category" required>
        </div>
        <div class="col-md-4 d-grid">
          <button type="submit" name="addCategory" class="btn btn-primary">Add Category</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Categories Nav -->

  <ul class="nav nav-pills mb-4">
    <li class="nav-item">
      <a class="nav-link <?= $selectedCategory == 0 ? 'active' : '' ?>" href="index.php">All</a>
    </li>
      <?php foreach ($list as $cat): ?>
        <li class="nav-item">
          <a class="nav-link <?= ($selectedCategory == $cat['category_id']) ? 'active' : '' ?>"
            href="index.php?category=<?= $cat['category_id'] ?>">
            <?= htmlspecialchars($cat['category_name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
  </ul>


  <!-- Proposals Section -->
  <div class="row">
    <?php if (!empty($proposals)): ?>
      <?php foreach ($proposals as $p): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
              <?php if (!empty($p['proposal_image'])): ?>
                  <img src="<?php echo '../images/' . htmlspecialchars($p['proposal_image']); ?>"
                      class="img-fluid" alt="">
              <?php else: ?>
                  <img src="https://via.placeholder.com/300x200?text=No+Image"
                      class="card-img-top" alt="No Image">
              <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['description']) ?></h5>
              <p class="card-text">
                Price: <?= $p['min_price'] ?> - <?= $p['max_price'] ?><br>
                <!-- Views: <?= $p['view_count'] ?> -->
              </p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">No proposals found for this category.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
