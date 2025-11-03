<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

function json($data) { echo json_encode($data); exit; }

$action = $_REQUEST['action'] ?? '';
if (!$action) json(['status'=>'error','message'=>'No action specified']);

if ($action === 'register') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $firstname = trim($input['firstname'] ?? '');
    $lastname = trim($input['lastname'] ?? '');
    $password = $input['password'] ?? '';
    $role = $input['role'] ?? 'user';

    if (!in_array($role, ['user', 'superadmin'])) {
        json(['success'=>false,'message'=>'Invalid role for registration.']);
    }
    if (!$username || !$firstname || !$lastname || !$password) {
        json(['success'=>false,'message'=>'All fields required.']);
    }

    // check exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) json(['success'=>false,'message'=>'Username already exists.']);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, role, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $firstname, $lastname, $role, $hash]);

    json(['success'=>true,'message'=>'Registered successfully.']);
}

if ($action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$password) json(['success'=>false,'message'=>'Username & password required.']);

    $stmt = $pdo->prepare("SELECT id, username, firstname, lastname, role, password, is_suspended FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) json(['success'=>false,'message'=>'Invalid credentials.']);

    if ($user['is_suspended']) json(['success'=>false,'message'=>'Account suspended.']);

    if (!password_verify($password, $user['password'])) json(['success'=>false,'message'=>'Invalid credentials.']);

    // set session
    $_SESSION['user'] = [
        'id'=>$user['id'],
        'username'=>$user['username'],
        'firstname'=>$user['firstname'],
        'lastname'=>$user['lastname'],
        'role'=>$user['role']
    ];
    json(['success'=>true,'message'=>'Login successful.','user'=>$_SESSION['user']]);
}

if ($action === 'logout') {
    session_destroy();
    json(['success'=>true,'message'=>'Logged out.']);
}

if ($action === 'session') {
    if (!empty($_SESSION['user'])) {
        json(['success'=>true,'user'=>$_SESSION['user']]);
    } else {
        json(['success'=>false]);
    }
}

// protected: must be logged in and superadmin for some actions
$user = $_SESSION['user'] ?? null;

// get accounts (superadmin)
if ($action === 'get_accounts') {
    if (!$user || $user['role'] !== 'superadmin') json(['success'=>false,'message'=>'Unauthorized.']);
    $stmt = $pdo->query("SELECT id, username, firstname, lastname, role, is_suspended, created_at FROM users WHERE role IN ('user','admin') ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json(['success'=>true,'accounts'=>$rows]);
}

// add admin (superadmin)
if ($action === 'add_admin') {
    if (!$user || $user['role'] !== 'superadmin') json(['success'=>false,'message'=>'Unauthorized.']);
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $firstname = trim($input['firstname'] ?? '');
    $lastname = trim($input['lastname'] ?? '');
    $password = $input['password'] ?? '';

    if (!$username || !$firstname || !$lastname || !$password) json(['success'=>false,'message'=>'All fields required.']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) json(['success'=>false,'message'=>'Username already exists.']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, role, password) VALUES (?, ?, ?, 'admin', ?)");
    $stmt->execute([$username, $firstname, $lastname, $hash]);
    json(['success'=>true,'message'=>'Admin created.']);
}

// suspend/unsuspend account (superadmin)
if ($action === 'toggle_suspend') {
    if (!$user || $user['role'] !== 'superadmin') json(['success'=>false,'message'=>'Unauthorized.']);
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    if (!$id) json(['success'=>false,'message'=>'Invalid id.']);
    $stmt = $pdo->prepare("SELECT is_suspended FROM users WHERE id = ? AND role IN ('user','admin')");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) json(['success'=>false,'message'=>'Account not found or cannot suspend superadmin.']);
    $new = $row['is_suspended'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE users SET is_suspended = ? WHERE id = ?");
    $stmt->execute([$new, $id]);
    json(['success'=>true,'message'=>$new ? 'Account suspended.' : 'Account unsuspended.','is_suspended'=>$new]);
}

// fallback
// json(['success'=>false,'message'=>'Unknown action.']);


// --- ADDING PRODUCT HANDLERS ---
if ($action === 'addProduct') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $admin_id = $_POST['admin_id'] ?? '';

    // Validate
    if ($name === '' || $price === '' || $admin_id === '') {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Verify admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$admin_id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid admin account']);
        exit;
    }

    // Image upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'No image uploaded']);
        exit;
    }

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;
    $imagePath = 'uploads/' . $fileName;

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image type.']);
        exit;
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        exit;
    }

    // Insert Product
    $stmt = $pdo->prepare("INSERT INTO products (name, price, image, added_by) VALUES (?,?,?,?)");
    $stmt->execute([$name, $price, $imagePath, $admin_id]);

    // Notification
    $msg = "Admin added new product: $name - ₱$price";
    $stmt = $pdo->prepare("INSERT INTO notifications (type, message) VALUES ('product', ?)");
    $stmt->execute([$msg]);

    echo json_encode(['status' => 'success']);
    exit;
}

elseif ($action === 'getProducts') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}


// --- USER HANDLERS ---

elseif ($action === 'placeOrder') {
    $user_id = $_POST['user_id'];
    $cart = json_decode($_POST['cart'], true);
    $payment = $_POST['payment'];
    $total = $_POST['total'];
    $change = $payment - $total;

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, payment, change_amount) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $total, $payment, $change]);
    $order_id = $pdo->lastInsertId();

    foreach ($cart as $item) {
        $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?,?,?)")
             ->execute([$order_id, $item['id'], $item['qty']]);
    }

    $msg = "User placed an order: Total ₱$total, Payment ₱$payment, Change ₱$change";
    $pdo->prepare("INSERT INTO notifications (type, message) VALUES ('order', ?)")->execute([$msg]);

    echo json_encode(['status' => 'success']);
    exit;
}

// --- REPORTS ---

elseif ($action === 'getReports') {
    $start = $_GET['start'] ?? '';
    $end = $_GET['end'] ?? '';

    $sql = "SELECT o.*, u.firstname AS user 
            FROM orders o 
            JOIN users u ON o.user_id = u.id";

    if (!empty($start) && !empty($end)) {
        $sql .= " WHERE DATE(o.date_ordered) BETWEEN :start AND :end";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['start' => $start, 'end' => $end]);
    } else {
        $stmt = $pdo->query($sql . " ORDER BY o.id DESC");
    }

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}



// --- GENERATE PDF REPORT ---
elseif ($action === 'generateReportPDF') {
    require_once 'vendor/autoload.php'; // for mPDF or include ReportLab/PDF if using Python
    require_once 'db.php';

    $date_start = $_GET['date_start'] ?? null;
    $date_end = $_GET['date_end'] ?? null;

    $query = "
        SELECT o.id, u.firstname, u.lastname, o.total, o.payment, o.change_amount, o.date_ordered
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE 1
    ";

    $params = [];

    if ($date_start && $date_end) {
        $query .= " AND DATE(o.date_ordered) BETWEEN ? AND ?";
        $params = [$date_start, $date_end];
    }

    $query .= " ORDER BY o.date_ordered DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate PDF with reportlab (if using Python) or mPDF (if PHP)
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();

    $html = '<h3 style="text-align:center;">Order Transaction Report</h3>';
    if ($date_start && $date_end) {
        $html .= "<p style='text-align:center;'>Filtered from $date_start to $date_end</p>";
    }

    $html .= '
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
      <thead>
        <tr style="background:#f0f0f0;">
          <th>ID</th>
          <th>Customer</th>
          <th>Total (₱)</th>
          <th>Payment (₱)</th>
          <th>Change (₱)</th>
          <th>Date Ordered</th>
        </tr>
      </thead>
      <tbody>
    ';

    $total_sum = 0;
    foreach ($orders as $o) {
        $total_sum += $o['total'];
        $html .= "
          <tr>
            <td>{$o['id']}</td>
            <td>{$o['firstname']} {$o['lastname']}</td>
            <td>₱{$o['total']}</td>
            <td>₱{$o['payment']}</td>
            <td>₱{$o['change_amount']}</td>
            <td>{$o['date_ordered']}</td>
          </tr>
        ";
    }

    $html .= "
      <tr>
        <td colspan='2' align='right'><strong>Total Sales:</strong></td>
        <td colspan='4'><strong>₱" . number_format($total_sum, 2) . "</strong></td>
      </tr>
    ";

    $html .= '</tbody></table>';

    $mpdf->WriteHTML($html);
    $mpdf->Output('report.pdf', 'I');
    exit;
}


// --- NOTIFICATIONS ---

elseif ($action === 'getNotifications') {
    $stmt = $pdo->query("SELECT * FROM notifications ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// --- FALLBACK IF UNKNOWN ACTION ---
else {
    echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
    exit;
}

