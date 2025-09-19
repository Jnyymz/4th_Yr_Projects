<?php
session_start();
require_once "classes/Database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];

// ✅ Get student profile
$stmt = $conn->prepare("SELECT id, student_num, year_level FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$student_id = $student['id'];

// ✅ Get student’s enrolled courses
$stmt = $conn->prepare("
    SELECT sc.id AS sc_id, sc.course_id, c.course_name, c.course_time 
    FROM student_courses sc
    JOIN courses c ON sc.course_id = c.id
    WHERE sc.student_id = ?
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body class="bg-[#FFF6F2] min-h-screen font-sans">
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#DC143C;">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">🎓 Student Dashboard</a>
      <div>
        <a href="login.php" class="btn btn-light text-danger">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    
    <!-- Add Course Button -->
    <div class="mb-4">
      <a href="courses.php" class="btn btn-danger">➕ Add Course</a>
    </div>

    <!-- Courses -->
    <h3 class="text-danger mb-3">📚 My Enrolled Courses</h3>
    <div class="row">
      <?php while ($row = $courses->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <h5 class="card-title fw-bold"><?= htmlspecialchars($row['course_name']) ?></h5>
              <p class="text-muted"><?= $row['course_time'] ? date("h:i A", strtotime($row['course_time'])) : "No time set" ?></p>
              <a href="attendance.php?course_id=<?= $row['course_id'] ?>" class="btn btn-danger">View Attendance</a>

              <a href="core/handleForms.php?drop_course=<?= $row['sc_id'] ?>" class="btn btn-sm btn-outline-danger ms-2">Drop</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>
