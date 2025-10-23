<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? '';

function json($data) {
    echo json_encode($data);
    exit;
}

// helper: require logged in
function require_login() {
    if (empty($_SESSION['user'])) {
        http_response_code(401);
        json(['success' => false, 'message' => 'Unauthorized']);
    }
}

// helper: require admin
function require_admin() {
    require_login();
    if (!($_SESSION['user']['is_admin'] ?? false)) {
        http_response_code(403);
        json(['success' => false, 'message' => 'Forbidden']);
    }
}

switch($action) {
    case 'check_username':
        $username = trim($_GET['username'] ?? '');
        if ($username === '') json(['available' => false, 'message' => 'empty']);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $exists = (bool)$stmt->fetch();
        json(['available' => !$exists]);
        break;

    case 'register':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($data['username'] ?? '');
        $firstname = trim($data['firstname'] ?? '');
        $lastname = trim($data['lastname'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';
        $is_admin = !empty($data['is_admin']) ? 1 : 0;

        // server-side validation
        if ($username === '' || $firstname === '' || $lastname === '' || $password === '' || $confirm === '') {
            json(['success' => false, 'message' => 'Please fill all fields.']);
        }
        if (strlen($password) < 8) {
            json(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        }
        if ($password !== $confirm) {
            json(['success' => false, 'message' => 'Passwords do not match.']);
        }

        // unique username check
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        if ($stmt->fetch()) {
            json(['success' => false, 'message' => 'Username already taken.']);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (:u,:f,:l,:a,:p)");
        $insert->execute([
            ':u' => $username,
            ':f' => $firstname,
            ':l' => $lastname,
            ':a' => $is_admin,
            ':p' => $hash
        ]);

        // Auto-login after registration
        $id = $pdo->lastInsertId();
        $_SESSION['user'] = [
            'id' => (int)$id,
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'is_admin' => (bool)$is_admin
        ];

        json(['success' => true, 'message' => 'Registered', 'user' => $_SESSION['user']]);
        break;

    case 'login':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if ($username === '' || $password === '') {
            json(['success' => false, 'message' => 'Please fill all fields.']);
        }

        $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, is_admin, password FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            json(['success' => false, 'message' => 'Invalid credentials.']);
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'is_admin' => (bool)$user['is_admin']
        ];
        json(['success' => true, 'message' => 'Logged in', 'user' => $_SESSION['user']]);
        break;

    case 'logout':
        session_destroy();
        json(['success' => true]);
        break;

    case 'get_users':
        require_admin();
        $stmt = $pdo->query("SELECT id, username, firstname, lastname, is_admin, date_added FROM users ORDER BY date_added DESC");
        $users = $stmt->fetchAll();
        json(['success' => true, 'users' => $users]);
        break;

    case 'add_user':
        require_admin();
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($data['username'] ?? '');
        $firstname = trim($data['firstname'] ?? '');
        $lastname = trim($data['lastname'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';
        $is_admin = !empty($data['is_admin']) ? 1 : 0;

        if ($username === '' || $firstname === '' || $lastname === '' || $password === '' || $confirm === '') {
            json(['success' => false, 'message' => 'Please fill all fields.']);
        }
        if (strlen($password) < 8) {
            json(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        }
        if ($password !== $confirm) {
            json(['success' => false, 'message' => 'Passwords do not match.']);
        }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        if ($stmt->fetch()) {
            json(['success' => false, 'message' => 'Username already taken.']);
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, is_admin, password) VALUES (:u,:f,:l,:a,:p)");
        $stmt->execute([
            ':u' => $username,
            ':f' => $firstname,
            ':l' => $lastname,
            ':a' => $is_admin,
            ':p' => $hash
        ]);
        json(['success' => true, 'message' => 'User added.']);
        break;

    default:
        http_response_code(400);
        json(['success' => false, 'message' => 'Unknown action']);
}
