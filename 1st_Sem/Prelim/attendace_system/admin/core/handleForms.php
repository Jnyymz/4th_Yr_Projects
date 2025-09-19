<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/User.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Admin Login
    if (isset($_POST['adminLogin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = new User();
        if ($user->login($username, $password) && $user->role === "admin") {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;
            $_SESSION['username'] = $user->username;

            header("Location: ../admin/dashboard.php"); // admin dashboard
            exit;
        } else {
            $error = "Invalid admin credentials!";
            $_SESSION['error'] = $error;
            header("Location: ../login.php");
            exit;
        }
    }

    // Admin Registration
    if (isset($_POST['action']) && $_POST['action'] === "register_admin") {
        $db = new Database();
        $conn = $db->getConnection();

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = "admin";

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            header("Location: ../login.php");
            exit;
        } else {
            $_SESSION['error'] = "Admin registration failed.";
            header("Location: ../register.php");
            exit;
        }
    }
}

$db = new Database();
$conn = $db->getConnection();

// ✅ Add Course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    $course_time = $_POST['course_time'];

    if (!empty($course_name) && !empty($course_time)) {
        $stmt = $conn->prepare("INSERT INTO courses (course_name, course_time) VALUES (?, ?)");
        $stmt->bind_param("ss", $course_name, $course_time);
        $stmt->execute();
        header("Location: ../dashboard.php");
        exit;
    }
}

// ✅ Delete Course
if (isset($_GET['delete_course'])) {
    $course_id = intval($_GET['delete_course']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    header("Location: ../dashboard.php");
    exit;
}

// ✅ Fetch all courses
$courses = $conn->query("SELECT * FROM courses ORDER BY course_name ASC");


if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $attendance = $conn->query("
        SELECT a.id AS attendance_id, 
               u.username, 
               c.course_name, 
               c.course_time,
               s.year_level, 
               a.date, 
               a.status, 
               a.is_late,
               el.id AS excuse_id, 
               el.letter_text AS excuse_message,   -- ✅ FIXED COLUMN NAME
               el.status AS excuse_status
        FROM attendance a
        JOIN student_courses sc ON a.student_course_id = sc.id
        JOIN students s ON sc.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN courses c ON sc.course_id = c.id
        LEFT JOIN excuse_letters el ON el.attendance_id = a.id
        WHERE c.id = $course_id
        ORDER BY a.date DESC
    ");
}


// ✅ Handle Excuse Letter Actions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['excuse_action'])) {
    $excuse_id = intval($_POST['excuse_id']);
    $action = $_POST['excuse_action'] === 'accept' ? 'Accepted' : 'Rejected';

    $stmt = $conn->prepare("UPDATE excuse_letters SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $excuse_id);
    $stmt->execute();

    header("Location: ../dashboard.php");
    exit;
}

