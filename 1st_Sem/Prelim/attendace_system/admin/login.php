<?php
session_start();
require_once __DIR__ . "/core/handleForms.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FDEBD0] flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-lg w-96 border border-[#F7CAC9]">
    <h2 class="text-2xl font-bold mb-6 text-center text-[#DC143C]">Admin Login</h2>

    <?php if (!empty($error)): ?>
      <p class="text-[#DC143C] text-sm mb-3 bg-[#F7CAC9] p-2 rounded text-center"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Username</label>
        <input type="text" name="username" required 
          class="w-full p-2 border border-[#F7CAC9] rounded focus:outline-none focus:ring-2 focus:ring-[#F75270]">
      </div>
      <div>
        <label class="block text-sm font-medium text-[#DC143C]">Password</label>
        <input type="password" name="password" required 
          class="w-full p-2 border border-[#F7CAC9] rounded focus:outline-none focus:ring-2 focus:ring-[#F75270]">
      </div>
      <button type="submit" name="adminLogin"
        class="w-full bg-[#DC143C] text-white py-2 rounded-lg hover:bg-[#F75270] transition-colors">
        Login
      </button>
    </form>

    <p class="mt-6 text-sm text-center text-gray-600">
      Don’t have an account? 
      <a href="register.php" class="text-[#F75270] font-medium hover:underline">Register</a>
    </p>
  </div>
</body>
</html>
