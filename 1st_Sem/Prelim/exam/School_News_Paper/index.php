<?php require_once 'writer/classloader.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Publication Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- ✅ Modern Header (unchanged) -->
  <header class="bg-gradient-to-r from-green-700 to-green-500 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <h1 class="text-2xl md:text-3xl font-bold text-white tracking-wide">
        School Publication
      </h1>
      <nav class="hidden md:flex space-x-6">
        <a href="#" class="text-white font-medium hover:text-yellow-300 transition">Home</a>
        <a href="#" class="text-white font-medium hover:text-yellow-300 transition">Articles</a>
        <a href="#" class="text-white font-medium hover:text-yellow-300 transition">About</a>
        <a href="#" class="text-white font-medium hover:text-yellow-300 transition">Contact</a>
      </nav>
    </div>
  </header>

  <!-- ✅ Login Cards Section -->
  <section class="max-w-7xl mx-auto px-6 py-10">
    <h2 class="text-2xl font-bold mb-6">Login Access</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-6">
      
        <!-- Writer Login Card -->
        <a href="writer/login.php" 
        class="relative rounded-xl overflow-hidden shadow-md hover:shadow-lg transition group h-96 flex flex-col justify-end">
        
        <!-- Background image -->
        <div class="absolute inset-0 bg-cover bg-center" 
            style="background-image: url(https://i.pinimg.com/1200x/a5/d2/af/a5d2af4bc4872f1ebdd3a9374245bf4b.jpg);"></div>
        
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition"></div>

        <!-- Content -->
        <div class="relative z-10 text-center text-white p-6">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                class="h-12 w-12 mx-auto mb-3 text-green-400" fill="none" 
                viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <h3 class="text-xl font-bold">Writer Login</h3>
            <p class="text-sm">Access the writer's dashboard.</p>
        </div>
        </a>


        <!-- Admin Login Card -->
        <a href="admin/login.php" 
        class="relative rounded-xl overflow-hidden shadow-md hover:shadow-lg transition group h-96 flex flex-col justify-end">
        
        <!-- Background image -->
        <div class="absolute inset-0 bg-cover bg-center" 
            style="background-image: url(https://i.pinimg.com/736x/03/17/ac/0317ac1446824e563c53260cb8cd73f6.jpg);"></div>
        
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition"></div>

        <!-- Content -->
        <div class="relative z-10 text-center text-white p-6">
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                class="h-12 w-12 mx-auto mb-3 text-red-400" fill="none" 
                viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm8-1v2a7.002 7.002 0 01-6 6.93V21h-4v-2.07A7.002 7.002 0 014 12v-2h16z"/>
            </svg>
            <h3 class="text-xl font-bold">Admin Login</h3>
            <p class="text-sm">Access the admin control panel.</p>
        </div>
        </a>


    </div>
  </section>

  <!-- ✅ Latest Articles Section -->
    <section class="max-w-7xl mx-auto px-6 py-10">
    <h2 class="text-2xl font-bold mb-6">Latest Articles</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php $articles = $articleObj->getActiveArticles(); ?>
        <?php if (!empty($articles)) { ?>
        <?php foreach ($articles as $article) { ?>
            <article class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition flex flex-col">
            
            <!-- Optional image placeholder if you don’t store image in DB -->
            <img src="https://source.unsplash.com/400x250/?news,school" 
                alt="Article Image" 
                class="w-full h-40 object-cover">

            <div class="p-4 flex-1 flex flex-col">
                <h3 class="text-lg font-semibold mb-2">
                <?php echo htmlspecialchars($article['title']); ?>
                </h3>

                <?php if ($article['is_admin'] == 1) { ?>
                <p class="mb-2">
                    <small class="bg-green-600 text-white px-2 py-1 rounded">
                    Message From Admin
                    </small>
                </p>
                <?php } ?>

                <small class="text-gray-500 block mb-2">
                <strong><?php echo htmlspecialchars($article['username']); ?></strong> 
                – <?php echo htmlspecialchars($article['created_at']); ?>
                </small>

                <p class="text-gray-600 text-sm flex-1">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </p>
            </div>
            </article>
        <?php } ?>
        <?php } else { ?>
        <p class="col-span-4 text-center text-gray-500">No articles available.</p>
        <?php } ?>
    </div>
    </section>

</body>
</html>
