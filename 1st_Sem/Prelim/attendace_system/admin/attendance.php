<?php
require_once __DIR__ . "/core/handleForms.php";


$course_id = $_GET['course_id'] ?? 0;

// Fetch pending excuse letters for this course
$excuseQuery = $conn->prepare("
    SELECT el.id, el.letter_text, el.status, el.submitted_at, u.username AS student_name
    FROM excuse_letters el
    JOIN students s ON el.student_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE el.course_id = ? AND el.status = 'Pending'
    ORDER BY el.submitted_at DESC
");
$excuseQuery->bind_param("i", $course_id);
$excuseQuery->execute();
$excuseResult = $excuseQuery->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Attendance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .excuse-card { cursor: pointer; transition: all 0.2s ease; }
    .excuse-card:hover { background-color: #f8f9fa; }
    .excuse-message { display: none; margin-top: 10px; }
  </style>
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="text-danger fw-bold mb-4">
    📝 Attendance - <?= htmlspecialchars($_GET['course_id']) ?>
  </h2>

  <a href="dashboard.php" class="btn btn-outline-secondary mb-4">⬅ Back to Dashboard</a>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-danger">
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Schedule</th>
            <th>Year Level</th>
            <th>Date</th>
            <th>Status</th>
            <th>Late</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $attendance->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['course_name']) ?></td>
              <td><?= $row['course_time'] ? date("h:i A", strtotime($row['course_time'])) : "-" ?></td>
              <td><?= htmlspecialchars($row['year_level']) ?></td>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td>
                <?php if ($row['status'] === 'Absent'): ?>
                  <span class="badge bg-danger">Absent</span>
                  <?php if ($row['excuse_status'] === 'Accepted'): ?>
                    <span class="badge bg-success ms-2">Excused</span>
                  <?php elseif ($row['excuse_status'] === 'Rejected'): ?>
                    <span class="badge bg-dark ms-2">Rejected Letter</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="badge bg-primary"><?= htmlspecialchars($row['status']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= $row['is_late'] ? '<span class="text-danger">Yes</span>' : 'No' ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Excuse Letters Section -->
  <h4 class="mb-3">Pending Excuse Letters</h4>
    <div class="row g-3">
    <?php while ($letter = $excuseResult->fetch_assoc()): ?>
        <div class="col-md-4">
        <div class="card excuse-card shadow-sm p-3" data-id="<?= $letter['id'] ?>">
            <strong><?= htmlspecialchars($letter['student_name']) ?></strong>
            <small class="text-muted"><?= date("Y-m-d H:i", strtotime($letter['submitted_at'])) ?></small>


            <div class="excuse-message mt-2">
            <p><?= nl2br(htmlspecialchars($letter['letter_text'])) ?></p>
            <form method="POST" action="core/handleForms.php" class="d-inline">
                <input type="hidden" name="excuse_action" value="accept">
                <input type="hidden" name="excuse_id" value="<?= $letter['id'] ?>">
                <button type="submit" class="btn btn-success btn-sm">Accept</button>
            </form>
            <form method="POST" action="core/handleForms.php" class="d-inline">
                <input type="hidden" name="excuse_action" value="reject">
                <input type="hidden" name="excuse_id" value="<?= $letter['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
            </form>
            </div>
        </div>
        </div>
    <?php endwhile; ?>
    </div>

<script>
$(document).ready(function() {
    $(".excuse-card").dblclick(function() {
        $(this).find(".excuse-message").slideToggle();
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
