<?php
session_start();
include 'dbconfig.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

/* ===================== LOGIN ===================== */
if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
    exit;
}

/* ===================== REGISTER ===================== */
if ($action === 'register') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    }
    exit;
}

/* ===================== PROFILE MANAGEMENT ===================== */
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

/* ===================== GET PROFILE ===================== */
if ($action === 'get_profile') {
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();

    $sections = $conn->prepare("SELECT * FROM profile_sections WHERE user_id = ?");
    $sections->bind_param("i", $user_id);
    $sections->execute();

    echo json_encode([
        'profile' => $profile,
        'sections' => $sections->get_result()->fetch_all(MYSQLI_ASSOC)
    ]);
    exit;
}

/* ===================== UPDATE PROFILE TEXT ===================== */
if ($action === 'update_profile_text') {
    $field = $_POST['field'];
    $value = $_POST['value'];
    $allowed = ['heading','subheading','about_text'];
    if (!in_array($field, $allowed)) exit;

    $stmt = $conn->prepare("UPDATE user_profiles SET $field = ? WHERE user_id = ?");
    $stmt->bind_param("si", $value, $user_id);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== UPDATE CONTACT ===================== */
if ($action === 'update_contact') {
    $field = $_POST['field'];
    $value = $_POST['value'];
    $allowed = ['github_link','email_link','phone_number'];
    if (!in_array($field, $allowed)) exit;

    $stmt = $conn->prepare("UPDATE user_profiles SET $field = ? WHERE user_id = ?");
    $stmt->bind_param("si", $value, $user_id);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== UPDATE IMAGE ===================== */
if ($action === 'update_image') {
    $image = $_POST['image_path'] ?? '';
    $stmt = $conn->prepare("UPDATE user_profiles SET image_path = ? WHERE user_id = ?");
    $stmt->bind_param("si", $image, $user_id);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}
 /* ===================== SAVE PROFILE ===================== */
if ($action === 'save_profile') {
    $heading = $_POST['heading'] ?? '';
    $subheading = $_POST['subheading'] ?? '';
    $about_text = $_POST['about_text'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $email_link = $_POST['email_link'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $image_path = $_POST['image_path'] ?? '';

    // Insert new row if user_id doesn't exist, otherwise update
    $stmt = $conn->prepare("
        INSERT INTO user_profiles (user_id, heading, subheading, about_text, github_link, email_link, phone_number, image_path)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            heading=VALUES(heading),
            subheading=VALUES(subheading),
            about_text=VALUES(about_text),
            github_link=VALUES(github_link),
            email_link=VALUES(email_link),
            phone_number=VALUES(phone_number),
            image_path=VALUES(image_path)
    ");
    $stmt->bind_param("isssssss", $user_id, $heading, $subheading, $about_text, $github_link, $email_link, $phone_number, $image_path);

    if ($stmt->execute()) {
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>$conn->error]);
    }
    exit;
}



/* ===================== ADD PROFILE SECTION ===================== */
if ($action === 'add_section') {
    $title = $_POST['title'] ?? '';
    $desc  = $_POST['description'] ?? '';

    $stmt = $conn->prepare("INSERT INTO profile_sections (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $desc);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== UPDATE PROFILE SECTION ===================== */
if ($action === 'update_section') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';

    $stmt = $conn->prepare("UPDATE profile_sections SET title = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $desc, $id, $user_id);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== DELETE PROFILE SECTION ===================== */
if ($action === 'delete_section') {
    $id = $_POST['id'] ?? 0;

    $stmt = $conn->prepare("DELETE FROM profile_sections WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();

    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== SKILLS MANAGEMENT ===================== */
if($action === 'get_skills'){
    $stmt = $conn->prepare("SELECT id,title,description FROM skills WHERE user_id=? ORDER BY created_at DESC");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while($row=$result->fetch_assoc()){ $data[] = $row; }
    echo json_encode(['status'=>'success','data'=>$data]);
    exit;
}

/* ===================== ADD SKILL ===================== */
if($action === 'add_skill'){
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $stmt = $conn->prepare("INSERT INTO skills (user_id,title,description) VALUES (?,?,?)");
    $stmt->bind_param("iss",$user_id,$title,$desc);
    $stmt->execute();
    echo json_encode(['status'=>'success','id'=>$stmt->insert_id]);
    exit;
}

/* ===================== UPDATE SKILL ===================== */
if($action === 'update_skill'){
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $stmt = $conn->prepare("UPDATE skills SET title=?,description=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssii",$title,$desc,$id,$user_id);
    $stmt->execute();
    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== DELETE SKILL ===================== */
if($action === 'delete_skill'){
    $id = $_POST['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM skills WHERE id=? AND user_id=?");
    $stmt->bind_param("ii",$id,$user_id);
    $stmt->execute();
    echo json_encode(['status'=>'success']);
    exit;
}

/* ===================== PROJECTS MANAGEMENT ===================== */
if ($action === 'get_projects') {
    $stmt = $conn->prepare("SELECT id,title,description,github_link FROM projects WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

/* ===================== ADD PROJECT ===================== */
if ($action === 'add_project') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $github = $_POST['github'] ?? '';

    $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, github_link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $description, $github);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}

/* ===================== UPDATE PROJECT ===================== */
if ($action === 'update_project') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $github = $_POST['github'] ?? '';

    $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, github_link = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssii", $title, $description, $github, $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

/* ===================== DELETE PROJECT ===================== */
if ($action === 'delete_project') {
    $id = $_POST['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

/* ===================== EDUCATION MANAGEMENT ===================== */
if ($action === 'get_education') {
    $stmt = $conn->prepare("SELECT id,title,description FROM education WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($action === 'add_education') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("INSERT INTO education (user_id,title,description) VALUES (?,?,?)");
    $stmt->bind_param("iss", $user_id, $title, $description);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    exit;
}

if ($action === 'update_education') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("UPDATE education SET title = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $description, $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'delete_education') {
    $id = $_POST['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM education WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

/* ===================== WORK EXPOSURE MANAGEMENT ===================== */
if ($action === 'get_work_exposure') {
    $stmt = $conn->prepare("SELECT id,title,description FROM work_exposure WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($action === 'add_work_exposure') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("INSERT INTO work_exposure (user_id,title,description) VALUES (?,?,?)");
    $stmt->bind_param("iss", $user_id, $title, $description);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    exit;
}

if ($action === 'update_work_exposure') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("UPDATE work_exposure SET title = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $description, $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'delete_work_exposure') {
    $id = $_POST['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM work_exposure WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

/* ===================== SEMINARS MANAGEMENT ===================== */
if ($action === 'get_seminars') {
    $stmt = $conn->prepare("SELECT id,title,description FROM seminars WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($action === 'add_seminar') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("INSERT INTO seminars (user_id,title,description) VALUES (?,?,?)");
    $stmt->bind_param("iss", $user_id, $title, $description);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    exit;
}

if ($action === 'update_seminar') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $stmt = $conn->prepare("UPDATE seminars SET title = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $description, $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'delete_seminar') {
    $id = $_POST['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM seminars WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success']);
    exit;
}


echo json_encode(['status'=>'error','message'=>'Invalid action']);