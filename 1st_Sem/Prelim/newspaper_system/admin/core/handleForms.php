<?php
require_once '../classloader.php'; 

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password)) {
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = "An error occured with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = $username . " as username is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}
	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {

		if ($userObj->loginUser($email, $password)) {
			header("Location: ../index.php");
		}
		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}


function getImageFullPath($relativePath) {
    // __DIR__ is admin/core
    // project root = two levels up from admin/core => __DIR__ . '/../../'
    return realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($relativePath, '/'));
}


if (isset($_POST['insertArticleBtn'])) {
    $title = isset($_POST['title']) ? trim(htmlspecialchars($_POST['title'])) : '';
    $description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : '';
    $author_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $image_path = null;

    if (!$author_id) {
        $_SESSION['status'] = '400';
        $_SESSION['message'] = 'You must be logged in to submit an article.';
        header("Location: ../index.php");
        exit;
    }

    if (!empty($_FILES['article_image']['name']) && is_uploaded_file($_FILES['article_image']['tmp_name'])) {
        $uploadDir = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['article_image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $fileExtension;
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['article_image']['tmp_name'], $targetFile)) {
            // Save relative path for DB (example: img/filename.png)
            $image_path = 'img/' . $fileName;
        }
    }

    if ($articleObj->createArticle($title, $description, $author_id, $image_path)) {
        $_SESSION['status'] = "200";
        $_SESSION['message'] = "Article created successfully!";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['status'] = "500";
        $_SESSION['message'] = "Error saving article!";
        header("Location: ../index.php");
        exit;
    }
}


if (isset($_POST['editArticleBtn'])) {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;

    if ($article_id && $articleObj->updateArticle($article_id, $title, $description)) {
        header("Location: ../articles_submitted.php");
        exit;
    } else {
        $_SESSION['message'] = "Failed to update article.";
        header("Location: ../articles_submitted.php");
        exit;
    }
}

if (isset($_POST['deleteArticleBtn']) || isset($_GET['delete'])) {
    // Accept either POST or GET (POST preferred)
    $article_id = 0;
    if (isset($_POST['deleteArticleBtn']) && isset($_POST['article_id'])) {
        $article_id = (int) $_POST['article_id'];
    } elseif (isset($_GET['delete'])) {
        $article_id = (int) $_GET['delete'];
    }

    if ($article_id <= 0) {
        if (isset($_POST['deleteArticleBtn'])) {
            echo "invalid_id";
            exit;
        } else {
            $_SESSION['message'] = "Invalid article id.";
            header("Location: ../index.php");
            exit;
        }
    }

    // Fetch article row using the Article model (single)
    $article = $articleObj->getArticles($article_id); 

    if (!$article) {
        if (isset($_POST['deleteArticleBtn'])) {
            echo "not_found";
            exit;
        } else {
            $_SESSION['message'] = "Article not found.";
            header("Location: ../index.php");
            exit;
        }
    }

    // Save the relative image path (DB column name used in createArticle was 'image_path')
    $relativeImagePath = isset($article['image_path']) ? $article['image_path'] : null;

    // Delete DB row via model
    $deleted = $articleObj->deleteArticle($article_id); 

    if ($deleted) {
        // Remove image file if exists
        if (!empty($relativeImagePath)) {
            $full = getImageFullPath($relativeImagePath);
            if ($full && file_exists($full)) {
                @unlink($full);
            }
        }

        // $articleObj->logDeletedArticle($article_id, $article['author_id'], $article['title']);

        if (isset($_POST['deleteArticleBtn'])) {
            echo "success";
            exit;
        } else {
            $_SESSION['message'] = "Article deleted successfully!";
            header("Location: ../index.php");
            exit;
        }
    } else {
        if (isset($_POST['deleteArticleBtn'])) {
            echo "error";
            exit;
        } else {
            $_SESSION['message'] = "Failed to delete article.";
            header("Location: ../index.php");
            exit;
        }
    }
}

/*
 * UPDATE VISIBILITY (AJAX)
 * expects: article_id, status, updateArticleVisibility = 1
 */
if (isset($_POST['updateArticleVisibility'])) {
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    if ($article_id > 0) {
        $res = $articleObj->updateArticleVisibility($article_id, $status);
        // echo model response directly (your JS expects truthy)
        echo $res ? "success" : "error";
    } else {
        echo "invalid";
    }
    exit;
}


header("Location: ../index.php");
exit;
