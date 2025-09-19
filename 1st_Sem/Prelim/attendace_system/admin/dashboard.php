<?php
require_once __DIR__ . "/core/handleForms.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FFF6F2] min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="bg-[#E63946] text-white px-6 py-4 flex justify-between items-center shadow-md">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <a href="login.php" 
       class="bg-white text-[#E63946] px-4 py-2 rounded-lg font-semibold shadow hover:bg-gray-100 transition">
       Logout
    </a>
  </nav>

  <div class="container mx-auto px-6 py-10">

    <!-- Add Course Card -->
    <div class="bg-white rounded-2xl shadow p-6 mb-10">
      <h2 class="text-xl font-bold text-[#E63946] flex items-center gap-2">
        ➕ Add New Course
      </h2>
      <form method="POST" action="core/handleForms.php" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <input type="text" name="course_name" 
               class="border rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#E63946]" 
               placeholder="Course Name" required>
        <input type="time" name="course_time" 
               class="border rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#E63946]" 
               required>
        <button type="submit" name="add_course" 
                class="bg-[#E63946] text-white font-semibold rounded-xl px-4 py-2 hover:bg-red-600 transition">
          Add
        </button>
      </form>
    </div>

    <!-- All Courses -->
    <h3 class="text-2xl font-bold text-[#E63946] mb-6 flex items-center gap-2">
      📚 All Courses
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php while ($row = $courses->fetch_assoc()): ?>
        <div class="bg-white rounded-2xl shadow p-6 flex flex-col justify-between">
          <div>
            <h4 class="text-lg font-bold text-gray-800">
              <?= htmlspecialchars($row['course_name']) ?>
            </h4>
            <p class="text-gray-500 mt-1">
              <?= $row['course_time'] ? date("h:i A", strtotime($row['course_time'])) : "No time set" ?>
            </p>
          </div>
          <div class="mt-4 flex justify-center gap-3">
            <a href="attendance.php?course_id=<?= $row['id'] ?>" 
               class="bg-[#E63946] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-600 transition">
               View Attendance
            </a>
            <a href="core/handleForms.php?delete_course=<?= $row['id'] ?>" 
               class="border border-[#E63946] text-[#E63946] px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition">
               Delete
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  </div>

</body>
</html>
