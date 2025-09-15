<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}  
?>
<!doctype html>
  <html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" 
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" 
        crossorigin="anonymous">

    <!-- Your Custom CSS (with cache-busting) -->
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">

  </head>
  <body id="aricle-body" style="background-color: #f4f6f8">
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">

      <!-- Welcome Section -->
      <div class="welcome-section text-center">
          <h2 class="welcome-title">Double Click the Article to <span class="highlight">Edit</span>!</h2>
      </div>

      <!-- Two-column layout -->
      <div class="article-layout">

        <!-- Left column: Form -->
        <div class="article-form" style="background-color: #f4f6f8">
          <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                  <input type="text" class="form-control mt-4" name="title" placeholder="Input title here">
              </div>
              <div class="form-group">
                  <textarea name="description" class="form-control mt-4" placeholder="Submit an article!"></textarea>
              </div>
              <div class="form-group">
                  <input type="file" class="form-control-file mt-4" name="article_image" accept="image/*">
              </div>
              <input type="submit" class="btn btn-primary form-control mt-4 mb-4" name="insertArticleBtn" value="Submit Article">
          </form>
        </div>

        <!-- Right column: Articles -->
        <div class="articles-column">
          <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
          <?php if (!empty($articles)) : ?>
          <div class="articles-grid">
            <?php foreach ($articles as $article) { ?>
              <div class="card article-card shadow">
                <?php if (!empty($article['image_path'])) { ?>
                  <img src="<?php echo $article['image_path']; ?>" alt="Article Image">
                <?php } ?>
                <div class="card-body">
                  <h5><?php echo htmlspecialchars($article['title']); ?></h5>
                  <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($article['username']); ?> - <?php echo $article['created_at']; ?></small>

                  <?php if ($article['is_active'] == 0) { ?>
                    <p class="text-danger">Status: PENDING</p>
                  <?php } elseif ($article['is_active'] == 1) { ?>
                    <p class="text-success">Status: ACTIVE</p>
                  <?php } ?>

                  <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>

                  <!-- Delete Form -->
                  <form class="deleteArticleForm">
                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                    <input type="submit" class="btn btn-danger float-right mb-2 deleteArticleBtn" value="Delete">
                  </form>

                  <!-- Edit Form -->
                  <div class="updateArticleForm d-none">
                    <h6>Edit Article</h6>
                    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                      <div class="form-group mt-2">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
                      </div>
                      <div class="form-group">
                        <textarea name="description" class="form-control"><?php echo htmlspecialchars($article['content']); ?></textarea>
                      </div>
                      <div class="form-group">
                        <input type="file" class="form-control-file mt-2" name="article_image" accept="image/*">
                        <?php if (!empty($article['image_path'])) { ?>
                          <small>Current image: <a href="<?php echo $article['image_path']; ?>" target="_blank">View</a></small>
                        <?php } ?>
                      </div>
                      <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                      <input type="submit" class="btn btn-primary float-right mt-2" name="editArticleBtn" value="Update">
                    </form>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <?php else: ?>
            <p class="text-center mt-4">No articles found.</p>
          <?php endif; ?>
        </div>

      </div>
    </div>
    <script>
      $('.articleCard').on('dblclick', function (event) {
        var updateArticleForm = $(this).find('.updateArticleForm');
        updateArticleForm.toggleClass('d-none');
      });

      $('.deleteArticleForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          deleteArticleBtn: 1
        }
        if (confirm("Are you sure you want to delete this article?")) {
          $.ajax({
            type:"POST",
            url: "core/handleForms.php",
            data:formData,
            success: function (data) {
              if (data) {
                location.reload();
              }
              else{
                alert("Deletion failed");
              }
            }
          })
        }
      })
    </script>
  </body>
</html>