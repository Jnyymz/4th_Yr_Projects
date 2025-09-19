<?php
session_start();
require_once "classes/Database.php";
require_once "classes/User.php";
require_once "classes/Attendance.php";
require_once "core/handleForms.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDEBD0] flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-96 border border-[#F7CAC9]">
    <h2 class="text-2xl font-bold mb-6 text-center text-[#DC143C]">Student Register</h2>

    <?php if (!empty($_SESSION['error'])): ?>
      <p class="text-[#DC143C] text-sm mb-3 bg-[#F7CAC9] p-2 rounded text-center">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </p>
    <?php endif; ?>

    <form method="POST" action="core/handleForms.php" class="space-y-4">
      <input type="hidden" name="action" value="register_student">
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Username</label>
        <input type="text" name="username" required class="w-full p-2 border rounded">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Password</label>
        <input type="password" name="password" required class="w-full p-2 border rounded">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Student Number</label>
        <input type="text" name="student_num" required class="w-full p-2 border rounded">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Year Level</label>
        <select name="year_level" required class="w-full p-2 border rounded">
          <option value="1">1st Year</option>
          <option value="2">2nd Year</option>
          <option value="3">3rd Year</option>
          <option value="4">4th Year</option>
        </select>
      </div>
      <button type="submit" class="w-full bg-[#DC143C] text-white py-2 rounded-lg">
        Register
      </button>
    </form>
    <p class="mt-6 text-sm text-center text-gray-600">
      Already have an account? 
      <a href="login.php" class="text-[#F75270] font-medium hover:underline">Login</a>
    </p>
  </div>
</body>
</html>
