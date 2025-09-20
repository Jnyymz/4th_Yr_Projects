<?php  

// header('Content-Type: application/json');
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); 

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




if (isset($_POST['insertArticleBtn'])) {
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $author_id   = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $image_path  = null;

    // Upload image to /img folder
    $uploadDir = dirname(__DIR__) . '/img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (!empty($_FILES['article_image']['name'])) {
        $fileName   = time() . '_' . basename($_FILES['article_image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['article_image']['tmp_name'], $targetFile)) {
            $image_path = 'img/' . $fileName;
        }
    }

    // ✅ Include category_id when creating the article
    if ($articleObj->createArticle($title, $description, $author_id, $category_id, $image_path)) {
        header("Location: ../index.php");
        exit;
    }
}


// start here
$uploadDir = dirname(__DIR__) . '/img/'; // root/img/
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$fileName = time() . '_' . basename($_FILES['article_image']['name']);
$targetFile = $uploadDir . $fileName;

$image_path = null;
if (move_uploaded_file($_FILES['article_image']['tmp_name'], $targetFile)) {
    $image_path = 'img/' . $fileName; // relative path for DB
}


if (isset($_POST['editArticleBtn'])) {
    $id = $_POST['article_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $imagePath = null;

    // ✅ check if a file was uploaded
    if (!empty($_FILES['image_path']['name'])) {
		$targetDir = "../img/";  // ✅ force to img/
		$fileName = time() . "_" . basename($_FILES['image_path']['name']);
		$targetFilePath = $targetDir . $fileName;

		if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFilePath)) {
			$imagePath = "img/" . $fileName; // ✅ store relative path
		}
	}



    // ✅ pass null if no new image, so updateArticle can keep old one
    $articleObj->updateArticle($id, $title, $content, $imagePath);

    header("Location: ../articles_submitted.php");
    exit;
}


if (isset($_POST['deleteArticleBtn'])) {
	$article_id = $_POST['article_id'];
	echo $articleObj->deleteArticle($article_id);
}



// For Access
if (isset($_POST['requestAccessBtn'])) {
    $article_id = $_POST['article_id'];
    $owner_id = $_POST['owner_id'];
    $requester_id = $_SESSION['user_id'];

    if ($articleObj->requestAccess($article_id, $requester_id, $owner_id)) {
        $_SESSION['message'] = "Request sent successfully!";
    } else {
        $_SESSION['message'] = "Error sending request!";
    }
    header("Location: ../index.php");
    exit;
}

// ✅ Owner accepts/rejects
if (isset($_POST['updateRequestStatusBtn'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status']; // accepted / rejected

    if ($articleObj->updateRequestStatus($request_id, $status)) {
        $_SESSION['message'] = "Request updated!";
    } else {
        $_SESSION['message'] = "Error updating request!";
    }
    header("Location: ../notification.php");
    exit;
}



