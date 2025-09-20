<?php
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($userObj->isAdmin()) {
    header("Location: ../admin/index.php");
    exit();
}

// Fetch accessed articles
$articles = $articleObj->getAccessedArticles($_SESSION['user_id']);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

  <!-- Custom CSS -->
  <link href="styles.css" rel="stylesheet">

  <title>Accessed Articles</title>
</head>
<body id="article-body" style="background-color: #f4f6f8">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">

  <!-- Welcome Section -->
  <div class="welcome-section text-center mb-4">
      <h2 class="welcome-title">Double Click the Article to <span class="highlight">Edit</span>!</h2>
  </div>

  <!-- Articles Grid -->
  <div class="row">
    <?php if (!empty($articles)) : ?>
      <?php foreach ($articles as $article) : ?>
        <div class="col-md-3 mb-4">
          <div class="card article-card shadow">
            <?php if (!empty($article['image_path'])) : ?>
              <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="Article Image" class="card-img-top" style="height:150px; object-fit:cover;">
            <?php endif; ?>
            <div class="card-body">
              <h5><?php echo htmlspecialchars($article['title']); ?></h5>
              <small class="text-muted d-block mb-2">
                  <?php echo htmlspecialchars($article['username'] ?? 'Unknown'); ?> - <?php echo $article['created_at']; ?>
              </small>
              <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>

              <!-- Edit Form -->
              <div class="updateArticleForm d-none">
                <h6>Edit Article</h6>
                <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                  <div class="form-group mt-2">
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
                  </div>
                  <div class="form-group">
                    <textarea name="content" class="form-control"><?php echo htmlspecialchars($article['content']); ?></textarea>
                  </div>
                  <div class="form-group">
                    <input type="file" class="form-control-file mt-2" name="image_path" accept="image/*">


                    <?php if (!empty($article['image_path'])) : ?>
                      <small>Current image: <a href="<?php echo $article['image_path']; ?>" target="_blank">View</a></small>
                    <?php endif; ?>
                  </div>

                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                  <input type="submit" class="btn btn-primary float-right mt-2" name="editArticleBtn" value="Update">
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center mt-4">No accessed articles found.</p>
    <?php endif; ?>
  </div>

</div>

<script>
  // Double-click to show/hide edit form
  $('.article-card').on('dblclick', function () {
    $(this).find('.updateArticleForm').toggleClass('d-none');
  });
</script>

</body>
</html>
