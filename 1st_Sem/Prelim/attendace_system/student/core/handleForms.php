<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/User.php";

$db = new Database();
$conn = $db->getConnection();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['studentLogin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = new User();
        if ($user->login($username, $password) && $user->role === "student") {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;
            $_SESSION['username'] = $user->username;

            header("Location: ../student/dashboard.php"); 
            exit;
        } else {
            $error = "Invalid student credentials!";
            $_SESSION['error'] = $error;
            header("Location: ../login.php");
            exit;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === "register_student") {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $student_num = $_POST['student_num'];
        $year_level = $_POST['year_level'];
        $role = "student";

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;

            // Insert into students
            $stmt2 = $conn->prepare("INSERT INTO students (user_id, student_num, year_level) VALUES (?, ?, ?)");
            $stmt2->bind_param("isi", $user_id, $student_num, $year_level);
            $stmt2->execute();

            header("Location: ../login.php");
            exit;
        } else {
            $_SESSION['error'] = "Student registration failed.";
            header("Location: ../register.php");
            exit;
        }
    }
}


// Enroll course
if (isset($_GET['enroll'], $_GET['student_id'])) {
    $course_id = intval($_GET['enroll']);
    $student_id = intval($_GET['student_id']);

    $stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();

    header("Location: ../dashboard.php");
    exit;
}

// Drop course
if (isset($_GET['drop_course'])) {
    $sc_id = intval($_GET['drop_course']);
    $stmt = $conn->prepare("DELETE FROM student_courses WHERE id = ?");
    $stmt->bind_param("i", $sc_id);
    $stmt->execute();

    header("Location: ../dashboard.php");
    exit;
}



if (isset($_POST['mark_attendance'])) {

    $student_course_id = $_POST['student_course_id'] ?? null;
    $course_id = $_POST['course_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $status = $_POST['status'] ?? null;
    $time_in = $_POST['time_in'] ?? null;

    // Validate required fields
    if (!$student_course_id || !$course_id || !$date || !$status) {
        $_SESSION['error'] = "Required fields missing.";
        header("Location: ../dashboard.php");
        exit;
    }

    // Calculate is_late
    $is_late = 0;
    if ($status === "Present" && $time_in && isset($_POST['course_time'])) {
        $course_time = $_POST['course_time'];
        if (strtotime($time_in) > strtotime($course_time) + (15 * 60)) {
            $is_late = 1;
        }
    }

    //  Insert attendance
    $stmt = $conn->prepare("INSERT INTO attendance (student_course_id, date, time_in, status, is_late) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $student_course_id, $date, $time_in, $status, $is_late);
    $stmt->execute();
    $attendance_id = $stmt->insert_id;

    //  If absent and excuse letter provided, insert into excuse_letters
    if ($status === "Absent" && !empty($_POST['excuse_letter'])) {
        $stmt2 = $conn->prepare("SELECT student_id FROM student_courses WHERE id = ?");
        $stmt2->bind_param("i", $student_course_id);
        $stmt2->execute();
        $student_row = $stmt2->get_result()->fetch_assoc();
        $student_id = $student_row['student_id'];

        $letter_text = $_POST['excuse_letter'];

        $stmt3 = $conn->prepare("INSERT INTO excuse_letters (attendance_id, student_id, course_id, letter_text) VALUES (?, ?, ?, ?)");
        $stmt3->bind_param("iiis", $attendance_id, $student_id, $course_id, $letter_text);
        $stmt3->execute();
    }

    header("Location: ../dashboard.php");
    exit;
}


// $student_course_id = $_POST['student_course_id'];
// $course_id = $_POST['course_id'];
// $date = $_POST['date'];
// $status = $_POST['status'];
// $time_in = $_POST['time_in'] ?? null;

// // 1️⃣ Insert attendance
// $stmt = $conn->prepare("
//     INSERT INTO attendance (student_course_id, date, time_in, status)
//     VALUES (?, ?, ?, ?)
// ");
// $stmt->bind_param("isss", $student_course_id, $date, $time_in, $status);
// $stmt->execute();
// $attendance_id = $stmt->insert_id;

// // 2️⃣ If absent and excuse letter provided, insert into excuse_letters
// if ($status === "Absent" && !empty($_POST['excuse_letter'])) {
//     // Get student_id from student_course
//     $stmt2 = $conn->prepare("SELECT student_id FROM student_courses WHERE id = ?");
//     $stmt2->bind_param("i", $student_course_id);
//     $stmt2->execute();
//     $student_row = $stmt2->get_result()->fetch_assoc();
//     $student_id = $student_row['student_id'];

//     $letter_text = $_POST['excuse_letter'];
//     $stmt3 = $conn->prepare("
//         INSERT INTO excuse_letters (attendance_id, student_id, course_id, letter_text)
//         VALUES (?, ?, ?, ?)
//     ");
//     $stmt3->bind_param("iiis", $attendance_id, $student_id, $course_id, $letter_text);
//     $stmt3->execute();
// }
