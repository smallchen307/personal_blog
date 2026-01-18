<?php
require 'db.php';
// 引入統一的 Header
require 'header.php'; 

// 撈取文章用於下方的列表
$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$all_posts = $stmt->fetchAll();

// 分類文章
$photo_posts = array_filter($all_posts, function($p) { return $p['type'] === 'photo'; });
$daily_posts = array_filter($all_posts, function($p) { return $p['type'] !== 'photo'; });

// 固定 Hero 背景圖路徑 (你可以換成你專案中固定的圖)
$fixed_hero_img = '/uploads/firework.jpg';
?>

    <section class="relative bg-cover bg-center h-[500px] md:h-[600px] flex items-center justify-center text-white"
             style="background-image: url('<?= $fixed_hero_img ?>');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative text-center p-6 max-w-2xl mx-auto rounded-lg">
            <h1 class="text-4xl md:text-[3.5rem] font-bold mb-4 leading-tight">
                紀錄我的創意與學習之旅
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
                <div class="card-bg rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition duration-300 border border-gray-100 bg-white">
                    <img src="<?= htmlspecialchars($post['cover_image']) ?: 'https://placehold.co/400x225?text=No+Image' ?>" alt="攝影" class="w-full h-48 object-cover">
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
                        <span class="text-sm font-semibold text-blue-600 mb-2 block uppercase"><?= htmlspecialchars($post['type']) ?></span>
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

<?php include 'footer.php'; ?>