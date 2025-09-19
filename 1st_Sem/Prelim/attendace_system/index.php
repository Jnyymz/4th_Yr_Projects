<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance System - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="container mx-auto px-6 lg:px-20">
        <h1 class="text-4xl font-bold text-center mb-12 text-gray-800">Attendance System</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            
            <!-- Student Card -->
            <a href="student/login.php" class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center justify-center hover:shadow-2xl transition duration-300 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.84 6.326L12 14z" />
                </svg>
                <h2 class="text-2xl font-semibold mb-2">Student</h2>
                <p class="text-gray-500">Login as a student to mark attendance and view your records.</p>
            </a>

            <!-- Admin Card -->
            <a href="admin/login.php" class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center justify-center hover:shadow-2xl transition duration-300 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20h12v-2a6 6 0 00-12 0v2z" />
                </svg>
                <h2 class="text-2xl font-semibold mb-2">Admin</h2>
                <p class="text-gray-500">Login as an admin to manage students, courses, and attendance.</p>
            </a>

        </div>
    </div>

</body>
</html>
