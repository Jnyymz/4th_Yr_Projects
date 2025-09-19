<?php
session_start();
require_once "classes/Database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

if (!isset($_GET['course_id'])) {
    echo "No course selected.";
    exit;
}

$course_id = intval($_GET['course_id']);
$user_id = $_SESSION['user_id'];

// ✅ Get student
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit;
}
$student_id = $student['id'];

// ✅ Get course info
$stmt = $conn->prepare("
    SELECT sc.id AS sc_id, c.course_name, c.course_time
    FROM student_courses sc
    JOIN courses c ON sc.course_id = c.id
    WHERE sc.student_id = ? AND sc.course_id = ?
");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    echo "Course not found or not enrolled.";
    exit;
}

// ✅ Attendance history with excuse status
$stmt = $conn->prepare("
    SELECT 
        a.id, 
        a.date, 
        a.time_in, 
        a.status, 
        a.is_late, 
        el.status AS excuse_status
    FROM attendance a
    LEFT JOIN excuse_letters el 
        ON a.id = el.attendance_id
    WHERE a.student_course_id = ?
    ORDER BY a.date DESC
");
$stmt->bind_param("i", $course['sc_id']);
$stmt->execute();
$attendance = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance - <?= htmlspecialchars($course['course_name']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function toggleExcuseLetter() {
      const status = document.querySelector("select[name='status']").value;
      document.getElementById("excuse-box").style.display = (status === "Absent") ? "block" : "none";
    }
  </script>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#DC143C;">
    <div class="container">
      <a class="navbar-brand fw-bold" href="student_dashboard.php">🎓 Student Dashboard</a>
      <div class="d-flex gap-2">
        <a href="dashboard.php" class="btn btn-outline-light">⬅ Back to Dashboard</a>
      </div>
    </div>
  </nav>

  <div class="container mx-auto px-6 lg:px-20 py-8 max-w-5xl">

    <!-- ✅ Course Info -->
    <div class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-gray-800">
        <?= htmlspecialchars($course['course_name']) ?>
      </h1>
      <p class="text-gray-500">
        <?= $course['course_time'] ? date("h:i A", strtotime($course['course_time'])) : "No time set" ?>
      </p>
    </div>

    <!-- ✅ Mark Attendance -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
      <h2 class="text-lg font-bold mb-4 text-gray-700">📝 Mark Attendance</h2>
      <form action="core/handleForms.php" method="POST" class="space-y-4">
        <input type="hidden" name="mark_attendance" value="1">
        <input type="hidden" name="student_course_id" value="<?= $course['sc_id'] ?>">
        <input type="hidden" name="course_id" value="<?= $course_id ?>">
        <input type="hidden" name="course_time" value="<?= $course['course_time'] ?>">

        <div>
          <label class="block text-gray-700">Date</label>
          <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
          <label class="block text-gray-700">Status</label>
          <select name="status" class="w-full border rounded px-3 py-2" onchange="toggleExcuseLetter()">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
          </select>
        </div>

        <div id="excuse-box" style="display:none;">
          <label class="block text-gray-700">Excuse Letter</label>
          <textarea name="excuse_letter" rows="4" class="w-full border rounded px-3 py-2" placeholder="Write your excuse here..."></textarea>
        </div>

        <div>
          <label class="block text-gray-700">Time In</label>
          <input type="time" name="time_in" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
          Submit
        </button>
      </form>
    </div>

    <!-- ✅ Attendance History -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-lg font-bold mb-4 text-gray-700">📅 Attendance History</h2>
      <table class="table-auto w-full border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="border px-4 py-2">Date</th>
            <th class="border px-4 py-2">Time In</th>
            <th class="border px-4 py-2">Status</th>
            <th class="border px-4 py-2">Late?</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $attendance->fetch_assoc()): ?>
            <tr>
              <td class="border px-4 py-2"><?= $row['date'] ?></td>
              <td class="border px-4 py-2"><?= $row['time_in'] ?? '-' ?></td>
              <td class="border px-4 py-2">
                <?php if ($row['status'] === 'Absent'): ?>
                    <?php if ($row['excuse_status'] === 'Accepted'): ?>
                        <span class="text-blue-600 font-bold">Excused</span>
                    <?php elseif ($row['excuse_status'] === 'Rejected'): ?>
                        <span class="text-red-600 font-bold">Rejected Letter</span>
                    <?php else: ?>
                        <span class="text-orange-600">Absent (Pending Excuse)</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-green-600 font-bold">Present</span>
                <?php endif; ?>
              </td>
              <td class="border px-4 py-2 <?= $row['is_late'] ? 'text-red-600 font-bold' : 'text-green-600' ?>">
                <?= $row['is_late'] ? "Yes" : "No" ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
