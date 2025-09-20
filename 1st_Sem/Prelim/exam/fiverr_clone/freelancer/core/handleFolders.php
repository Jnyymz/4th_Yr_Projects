<?php  
session_start();

require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$contact_number = htmlspecialchars(trim($_POST['contact_number']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password, $contact_number)) {
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

if (isset($_POST['updateUserBtn'])) {
	$contact_number = htmlspecialchars($_POST['contact_number']);
	$bio_description = htmlspecialchars($_POST['bio_description']);
	if ($userObj->updateUser($contact_number, $bio_description, $_SESSION['user_id'])) {
		$_SESSION['status'] = "200";
		$_SESSION['message'] = "Profile updated successfully!";
		header("Location: ../profile.php");
	}
}

// if (isset($_POST['insertNewProposalBtn'])) {

//     // Get file name
//     $fileName      = $_FILES['image']['name'];
//     $tempFileName  = $_FILES['image']['tmp_name'];
//     $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
//     $uniqueID      = sha1(md5(rand(1,9999999)));
//     $imageName     = $uniqueID . "." . $fileExtension;
//     $folder        = "../../images/" . $imageName;
// 	$user_id       = $_SESSION['user_id'];
//     if (move_uploaded_file($tempFileName, $folder)) {

//         if ($proposalObj->createProposal(
//                 $user_id,
//                 $description,
// 				$imageName,
//                 $min_price,
//                 $max_price,
//                 $category_id
//             )) {
//             $_SESSION['status']  = "200";
//             $_SESSION['message'] = "Proposal saved successfully!";
//             header("Location: ../index.php");
//             exit();
//         }
//     }
// }


if (isset($_POST['insertNewProposalBtn'])) {

    // Sanitize and validate input values
    $description = htmlspecialchars(trim($_POST['description']));
    $min_price   = floatval($_POST['min_price']);
    $max_price   = floatval($_POST['max_price']);
    $category_id = intval($_POST['category_id']);
    $user_id     = $_SESSION['user_id'];

    // Check if a file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $fileName      = $_FILES['image']['name'];
        $tempFileName  = $_FILES['image']['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt    = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // allowed extensions

        if (in_array($fileExtension, $allowedExt)) {
            $uniqueID  = sha1(uniqid(rand(), true));
            $imageName = $uniqueID . "." . $fileExtension;
            $folder    = "../../images/" . $imageName;

            if (move_uploaded_file($tempFileName, $folder)) {

                // Save proposal to the database
                if ($proposalObj->createProposal(
                    $user_id,
                    $description,
                    $imageName,
                    $min_price,
                    $max_price,
                    $category_id
                )) {
                    $_SESSION['status']  = "200";
                    $_SESSION['message'] = "Proposal saved successfully!";
                    header("Location: ../index.php");
                    exit();
                } else {
                    $_SESSION['status']  = "500";
                    $_SESSION['message'] = "Database error. Could not save proposal.";
                }

            } else {
                $_SESSION['status']  = "500";
                $_SESSION['message'] = "Failed to upload the image.";
            }
        } else {
            $_SESSION['status']  = "400";
            $_SESSION['message'] = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
        }

    } else {
        $_SESSION['status']  = "400";
        $_SESSION['message'] = "Please upload an image.";
    }
}


// if (isset($_POST['insertNewProposalBtn'])) {
// 		$user_id,
//         $description,
//         $min_price,     
//         $max_price,
//         $imageName,
//         $category_id


// 	// Get file name
// 	$fileName = $_FILES['image']['name'];

// 	// Get temporary file name
// 	$tempFileName = $_FILES['image']['tmp_name'];

// 	// Get file extension
// 	$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

// 	// Generate random characters for image name
// 	$uniqueID = sha1(md5(rand(1,9999999)));

// 	// Combine image name and file extension
// 	$imageName = $uniqueID.".".$fileExtension;

// 	// Specify path
// 	$folder = "../../images/".$imageName;

// 	// Move file to the specified path 
// 	if (move_uploaded_file($tempFileName, $folder)) {
// 		if ($proposalObj->createProposal($user_id, $description, $imageName, $min_price, $max_price, $category_id)) {
// 			$_SESSION['status'] = "200";
// 			$_SESSION['message'] = "Proposal saved successfully!";
// 			header("Location: ../index.php");
// 		}
// 	}
// }

// if (isset($_POST['insertNewProposalBtn'])) {
//     $user_id = $_SESSION['user_id'];
//     $description = $_POST['description'];
//     $min_price = $_POST['min_price'];
//     $max_price = $_POST['max_price'];
//     $category_id = $_POST['category_id']; // ✅ capture selected category

//     // handle image upload ...
//     $imageName = $_FILES['image']['name'];
//     $tmpName   = $_FILES['image']['tmp_name'];
//     move_uploaded_file($tmpName, "../images/" . $imageName);

//     $proposalObj->createProposal($user_id, $description, $min_price, $max_price, $imageName, $category_id);

//     $_SESSION['message'] = "Proposal added successfully!";
//     $_SESSION['status'] = "200";
//     header("Location: ../freelancer/index.php");
//     exit;
// }


if ($proposalObj->createProposal($user_id, $description, $min_price, $max_price, $image, $category_id)) {
    $_SESSION['message'] = "Proposal added successfully!";
    $_SESSION['status'] = "200";
    header("Location: ../index.php"); // redirect
    exit;
}


if (isset($_POST['updateProposalBtn'])) {
	$min_price = $_POST['min_price'];
	$max_price = $_POST['max_price'];
	$proposal_id = $_POST['proposal_id'];
	$description = htmlspecialchars($_POST['description']);
	if ($proposalObj->updateProposal($description, $min_price, $max_price, $proposal_id)) {
		$_SESSION['status'] = "200";
		$_SESSION['message'] = "Proposal updated successfully!";
		header("Location: ../your_proposals.php");
	}
}

if (isset($_POST['deleteProposalBtn'])) {
	$proposal_id = $_POST['proposal_id'];
	$image = $_POST['image'];

	if ($proposalObj->deleteProposal($proposal_id)) {
		// Delete file inside images folder
		unlink("../../images/".$image);
		
		$_SESSION['status'] = "200";
		$_SESSION['message'] = "Proposal deleted successfully!";
		header("Location: ../your_proposals.php");
	}
}

$category_id = $_POST['category_id'];
$proposalObj->createProposal($user_id, $description, $imageName, $min_price, $max_price, $category_id);
