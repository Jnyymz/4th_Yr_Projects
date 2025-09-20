<?php
session_start();
require_once 'classes/Categories.php';

if (!isset($_SESSION['second_admin'])) {
    header("Location: login.php");
    exit;
}

$catObj = new Categories();
$categories = $catObj->getAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Second Admin - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen">

    <!-- Header / Navbar -->
    <nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold">🌿 Second Admin Dashboard</h1>
        <form method="post" action="core/handleForms.php">
            <button 
                type="submit" 
                name="logoutBtn"
                class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-md shadow transition">
                Logout
            </button>
        </form>
    </nav>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto py-10 px-4">
        <h2 class="text-3xl font-bold text-green-800 mb-8 text-center">
            Welcome, Second Admin
        </h2>

        <!-- Add Category Form -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-10 border border-green-100">
            <h3 class="text-2xl font-semibold text-green-700 mb-4">Add New Category</h3>
            <form method="post" action="core/handleForms.php" class="space-y-4">
                <input 
                    type="text" 
                    name="category_name"
                    placeholder="Enter category name"
                    required
                    class="w-full px-4 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 transition"
                >
                <button 
                    type="submit"
                    name="insertCategoryBtn"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg shadow transition">
                    ➕ Add Category
                </button>
            </form>
        </div>

        <!-- Existing Categories Table -->
        <div class="bg-white shadow-md rounded-xl p-6 border border-green-100">
            <h3 class="text-2xl font-semibold text-green-700 mb-4">Existing Categories</h3>
            
            <?php if ($categories): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-green-100 text-green-800">
                                <th class="border border-green-200 px-4 py-2 text-left">ID</th>
                                <th class="border border-green-200 px-4 py-2 text-left">Name</th>
                                <th class="border border-green-200 px-4 py-2 text-left">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-green-50">
                                <td class="border border-green-200 px-4 py-2"><?= htmlspecialchars($cat['category_id']) ?></td>
                                <td class="border border-green-200 px-4 py-2"><?= htmlspecialchars($cat['name']) ?></td>
                                <td class="border border-green-200 px-4 py-2"><?= htmlspecialchars($cat['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-green-700 text-center">No categories yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
