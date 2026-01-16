<?php
require 'db.php';

// 1. 取得網址上的 slug (與你目前的 GitHub 邏輯一致)
$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: index.php');
    exit;
}

// 2. 撈取該篇文章資料
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - 我的視界</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f8f8; }
        .bg-primary { background-color: #0d1a26; } 
        .text-primary-light { color: #e0e7ff; } 
        .bg-secondary { background-color: #1a2b3c; } 
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
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="admin/post_manager.php" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">管理後台</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto py-12 px-4 max-w-4xl">
        <article class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if ($post['cover_image']): ?>
                <div class="w-full h-[400px] overflow-hidden">
                    <img src="<?= htmlspecialchars($post['cover_image']) ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="p-8 md:p-12">
                <div class="flex items-center space-x-4 mb-6">
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        <?= htmlspecialchars($post['type']) ?>
                    </span>
                    <span class="text-gray-400 text-sm">發布於 <?= date('Y/m/d', strtotime($post['created_at'])) ?></span>
                </div>

                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-8 leading-tight">
                    <?= htmlspecialchars($post['title']) ?>
                </h1>

                <div class="prose prose-slate max-w-none text-gray-700 leading-relaxed text-lg">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <div class="mt-16 pt-8 border-t border-gray-100 text-center">
                    <a href="index.php" class="inline-block bg-primary text-primary-light hover:bg-secondary py-3 px-8 rounded-full shadow-lg transition duration-300">
                        &larr; 返回首頁
                    </a>
                </div>
            </div>
        </article>
    </main>

    <footer class="bg-secondary text-primary-light py-8 px-4 mt-12">
        <div class="container mx-auto text-center">
            <p class="text-sm">&copy; <?= date('Y') ?> 我的視界 - All rights reserved.</p>
        </div>
    </footer>

</body>
</html>