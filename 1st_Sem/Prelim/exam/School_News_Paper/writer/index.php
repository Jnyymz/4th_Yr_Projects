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
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Custom CSS -->
    <link href="styles.css" rel="stylesheet">
    
    <title>Articles</title>
</head>
<body id="article-body">
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="welcome-section text-center mb-5">
            <h2 class="welcome-title">Hello, <span class="highlight"><?php echo $_SESSION['username']; ?></span>!</h2>
            <p class="welcome-subtitle">Check out the latest articles below.</p>
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
                <?php 
                    $articles = $articleObj->getActiveArticles(); 
                ?>

                <?php if (!empty($articles)) : ?>
                    <div class="articles-grid"> <!-- Add this wrapper for the 2-column grid -->
                        <?php foreach ($articles as $article) : ?>
                            <div class="card article-card shadow mb-4">
                                <?php if (!empty($article['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="Article Image">
                                <?php endif; ?>

                                <div class="card-body">
                                    <h5><?php echo htmlspecialchars($article['title']); ?></h5>

                                    <?php if (!empty($article['is_admin']) && $article['is_admin'] == 1) { ?>
                                        <span class="badge badge-primary mb-2">Message From Admin</span>
                                    <?php } ?>

                                    <small class="text-muted d-block mb-2">
                                        <strong><?php echo htmlspecialchars($article['username']); ?></strong> - <?php echo $article['created_at']; ?>
                                    </small>

                                    <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>

                                    <!-- Access request logic -->
                                    <?php
                                        if ($article['author_id'] != $_SESSION['user_id']) {
                                            $status = $articleObj->getRequestStatus($article['article_id'], $_SESSION['user_id']);

                                            if ($status == 'accepted') {
                                                echo "<p class='text-success'>You have access to this article.</p>";
                                            } elseif ($status == 'rejected') {
                                                echo "<p class='text-danger'>You were rejected access to this article.</p>";
                                            } elseif ($status == 'pending') {
                                                echo "<p class='text-warning'>Request pending...</p>";
                                            } else {
                                                echo '<form method="POST" action="core/handleForms.php">
                                                        <input type="hidden" name="article_id" value="'.$article['article_id'].'">
                                                        <input type="hidden" name="owner_id" value="'.$article['author_id'].'">
                                                        <button type="submit" name="requestAccessBtn" class="btn btn-sm btn-outline-primary">Request Access</button>
                                                    </form>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center mt-4">No articles found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
