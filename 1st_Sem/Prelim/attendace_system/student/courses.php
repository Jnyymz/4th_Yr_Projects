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

// ✅ Get student_id
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$student_id = $student['id'];

// ✅ Fetch all courses
$courses = $conn->query("SELECT * FROM courses");

// ✅ Fetch enrolled courses of this student
$enrolled = [];
$enrolledQuery = $conn->prepare("SELECT course_id FROM student_courses WHERE student_id = ?");
$enrolledQuery->bind_param("i", $student_id);
$enrolledQuery->execute();
$result = $enrolledQuery->get_result();
while ($row = $result->fetch_assoc()) {
    $enrolled[] = $row['course_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#DC143C;">
    <div class="container">
      <a class="navbar-brand fw-bold" href="student_dashboard.php">🎓 Student Dashboard</a>
      <div class="d-flex gap-2">
        <!-- Go Back to Dashboard Button -->
        <a href="dashboard.php" class="btn btn-outline-light">⬅ Back to Dashboard</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <h3 class="text-danger mb-4">📖 Available Courses</h3>
    <div class="row">
      <?php while ($row = $courses->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <h5 class="card-title fw-bold"><?= htmlspecialchars($row['course_name']) ?></h5>
              <p class="text-muted"><?= $row['course_time'] ? date("h:i A", strtotime($row['course_time'])) : "No time set" ?></p>

              <?php if (in_array($row['id'], $enrolled)): ?>
                <!-- Already Enrolled -->
                <button class="btn btn-sm btn-secondary" disabled>✅ Enrolled</button>
              <?php else: ?>
                <!-- Enroll Button -->
                <a href="core/handleForms.php?enroll=<?= $row['id'] ?>&student_id=<?= $student_id ?>" 
                   class="btn btn-sm btn-success">Enroll</a>
              <?php endif; ?>

            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>
