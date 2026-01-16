<?php
require 'db.php';

// 撈取所有已發布文章
$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$all_posts = $stmt->fetchAll();

// 分類文章（對應你模板的兩個區塊）
$photo_posts = array_filter($all_posts, function($p) { return $p['type'] === 'photo'; });
$daily_posts = array_filter($all_posts, function($p) { return $p['type'] !== 'photo'; });

// 取得最新一篇作為 Hero 背景（可選）
$hero_post = $all_posts[0] ?? null;
$hero_img = ($hero_post && $hero_post['cover_image']) ? $hero_post['cover_image'] : 'https://attach.mobile01.com/attach/202103/mobile01-52357bb5d1f6f06546ebbd73469a2afc.jpg?original=true';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的視界 - 個人綜合平台</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f8f8; }
        .bg-primary { background-color: #0d1a26; } 
        .text-primary-light { color: #e0e7ff; } 
        .bg-secondary { background-color: #1a2b3c; } 
        .btn-accent { background-color: #ef4444; } 
        .btn-accent:hover { background-color: #dc2626; }
        .card-bg { background-color: #ffffff; }
    </style>
</head>
<body class="antialiased">

    <header class="bg-primary text-primary-light p-4 shadow-lg">
        <nav class="container mx-auto flex justify-between items-center flex-wrap">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-2xl font-bold rounded-md hover:text-white transition duration-300">
                    <span class="text-red-500">我的</span>視界
                </a>
                <div class="hidden md:flex space-x-6 ml-8">
                    <a href="index.php" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">首頁</a>
                    <a href="#" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">攝影視界</a>
                    <a href="#" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">程式學習日誌</a>
                    <a href="#" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">關於我</a>
                    <a href="admin/post_manager.php" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">管理後台</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-white transition duration-300 hidden md:block px-3 py-2 rounded-md">會員中心</a>
                <a href="#" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart">
                        <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                </a>
                <button class="md:hidden text-primary-light focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu">
                        <line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <section class="relative bg-cover bg-center h-[500px] md:h-[600px] flex items-center justify-center text-white"
             style="background-image: url('<?= $hero_img ?>');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative text-center p-6 max-w-2xl mx-auto rounded-lg">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">
                <?= $hero_post ? htmlspecialchars($hero_post['title']) : '探索我的創意與學習之旅' ?>
            </h1>
            <p class="text-lg md:text-xl mb-8">這裡融合了我的攝影作品、學習歷程與知識分享。</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#photo-section" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300">
                    進入攝影視界
                </a>
                <a href="#daily-section" class="bg-white text-gray-900 hover:bg-gray-100 font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300">
                    查看學習日誌
                </a>
            </div>
        </div>
    </section>

    <main class="container mx-auto py-12 px-4">

        <section id="photo-section" class="mb-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">精選攝影內容</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach (array_slice($photo_posts, 0, 4) as $post): ?>
                <div class="card-bg rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition duration-300">
                    <img src="<?= htmlspecialchars($post['cover_image']) ?: 'https://placehold.co/400x225?text=No+Image' ?>" alt="攝影" class="w-full h-48 object-cover rounded-t-lg">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="text-gray-600 text-sm line-clamp-2"><?= mb_strimwidth(strip_tags($post['content']), 0, 60, "...") ?></p>
                        <a href="post_view.php?slug=<?= $post['slug'] ?>" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium transition duration-300">觀看文章 &rarr;</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="daily-section" class="mb-16 bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">最新學習日誌</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach (array_slice($daily_posts, 0, 4) as $post): ?>
                <div class="card-bg rounded-lg shadow-sm overflow-hidden border border-gray-200">
                    <div class="p-5">
                        <span class="text-sm font-semibold text-blue-600 mb-2 block">學習筆記 / <?= strtoupper($post['type']) ?></span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= mb_strimwidth(strip_tags($post['content']), 0, 100, "...") ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs"><?= date('Y/m/d', strtotime($post['created_at'])) ?></span>
                            <a href="post_view.php?slug=<?= $post['slug'] ?>" class="text-blue-500 hover:text-blue-600 font-medium transition duration-300">閱讀更多 &rarr;</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

    <footer class="bg-secondary text-primary-light py-8 px-4 rounded-t-lg">
        <div class="container mx-auto text-center md:flex md:justify-between md:items-center">
            <div class="mb-4 md:mb-0 text-left">
                <h3 class="text-lg font-bold mb-2">我的視界</h3>
                <p class="text-sm">&copy; <?= date('Y') ?> All rights reserved.</p>
            </div>
            <div class="flex justify-center space-x-6 text-sm">
                <a href="#" class="hover:text-white transition duration-300">聯絡我們</a>
                <a href="#" class="hover:text-white transition duration-300">隱私政策</a>
                <a href="admin/login.php" class="hover:text-white transition duration-300">管理員登入</a>
            </div>
        </div>
    </footer>

</body>
</html>