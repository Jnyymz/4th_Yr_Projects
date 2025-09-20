<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Second Admin - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-xl rounded-2xl w-full max-w-md p-8 border border-green-100">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-green-700">Second Admin Registration</h2>
            <p class="text-green-600 mt-1 text-sm">Create an account to manage categories</p>
        </div>

        <!-- Registration Form -->
        <form method="post" action="core/handleForms.php" class="space-y-5">
            <div>
                <label class="block text-green-700 text-sm mb-1 font-medium">Username</label>
                <input 
                    class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
                    type="text" 
                    name="username" 
                    placeholder="Enter your username" 
                    required>
            </div>

            <div>
                <label class="block text-green-700 text-sm mb-1 font-medium">Email</label>
                <input 
                    class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
                    type="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required>
            </div>

            <div>
                <label class="block text-green-700 text-sm mb-1 font-medium">Password</label>
                <input 
                    class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
                    type="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required>
            </div>

            <div>
                <label class="block text-green-700 text-sm mb-1 font-medium">Confirm Password</label>
                <input 
                    class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
                    type="password" 
                    name="confirm_password" 
                    placeholder="Confirm your password" 
                    required>
            </div>

            <button 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg shadow-md transition duration-200"
                name="registerBtn">
                Register
            </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-green-700 text-sm mt-6">
            Already have an account?
            <a href="login.php" class="text-green-600 font-semibold hover:underline">Login here</a>
        </p>
    </div>

</body>
</html>
