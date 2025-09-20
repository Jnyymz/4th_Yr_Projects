<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="w-full max-w-sm bg-white shadow-lg rounded-xl p-8">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Register</h2>

    <form method="post" action="core/handleForms.php" class="space-y-5">
      <!-- Username -->
      <div>
        <input type="text" name="username" placeholder="Username" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700
                 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
      </div>

      <!-- Email -->
      <div>
        <input type="email" name="email" placeholder="Email" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700
                 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
      </div>

      <!-- Password -->
      <div>
        <input type="password" name="password" placeholder="Password" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700
                 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
      </div>

      <!-- Button -->
      <button type="submit" name="register"
        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg
               transition duration-300 ease-in-out">
        Register
      </button>
    </form>

    <!-- Footer -->
    <div class="mt-6 text-center text-sm text-gray-600">
      <p>
        Already have an account?
        <a href="login.php"
           class="text-purple-600 hover:text-purple-800 font-medium transition-colors duration-200">
          login here!
        </a>
      </p>
    </div>
  </div>

</body>
</html>
