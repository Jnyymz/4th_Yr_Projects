<?php
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get pending access requests
$pendingRequests = $articleObj->getPendingRequests($_SESSION['user_id']);

// Get deleted articles notifications
$deletedArticles = $articleObj->getDeletedArticles($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Notifications</h2>

        <?php if (!empty($pendingRequests) || !empty($deletedArticles)) : ?>

            <!-- Pending Article Requests -->
            <?php foreach ($pendingRequests as $request) : ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p>
                            <strong><?php echo htmlspecialchars($request['username']); ?></strong> 
                            wants to access your article: 
                            <em><?php echo htmlspecialchars($request['title']); ?></em>
                        </p>

                        <form method="POST" action="core/handleForms.php" class="d-inline">
                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" name="updateRequestStatusBtn" class="btn btn-sm btn-success">Accept</button>
                        </form>

                        <form method="POST" action="core/handleForms.php" class="d-inline">
                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" name="updateRequestStatusBtn" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Admin Deleted Articles -->
            <?php foreach ($deletedArticles as $deleted) : ?>
                <div class="card mb-3 border-warning">
                    <div class="card-body">
                        <p class="text-warning mb-1">
                            <strong>The Admin deleted your article:</strong> 
                            <em><?php echo htmlspecialchars($deleted['title']); ?></em>
                        </p>
                        <small class="text-muted">Deleted at: <?php echo htmlspecialchars($deleted['deleted_at']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="text-muted">No pending notifications.</p>
        <?php endif; ?>
    </div>
</body>
</html>
