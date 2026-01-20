<?php
// category.php
require 'db.php';
require 'header.php';

// 1. 定義分類常數配置
$categoryConfigs = [
    'photo' => [
        'limit' => 8,
        'title' => '攝影視界',
        'desc'  => '透過鏡頭記錄生活的瞬間與光影',
        'default_img' => '/image/default_photo.webp'
    ],
    'blog'  => [
        'limit' => 12,
        'title' => '開發筆記',
        'desc'  => '探索技術與程式開發的紀錄',
        'default_img' => '/image/default_blog.jpg'
    ]
];

// 2. 取得網址參數並檢查合理性
$type = $_GET['type'] ?? 'blog';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

if (!array_key_exists($type, $categoryConfigs)) { $type = 'blog'; }

$config    = $categoryConfigs[$type];
$limit     = $config['limit'];
$offset    = ($page - 1) * $limit;
$pageTitle = $config['title'];
$pageDesc  = $config['desc'];

// 3. 核心邏輯：撈取資料
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE type = ? AND status = 'published'");
$countStmt->execute([$type]);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

$stmt = $pdo->prepare("
    SELECT * FROM posts 
    WHERE type = :type AND status = 'published' 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':type', $type, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<div class="bg-white min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 py-16">
        
        <header class="mb-16 text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-gray-500 text-xl"><?= htmlspecialchars($pageDesc) ?></p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($posts as $post): ?>
                <article class="flex flex-col bg-gray-900 rounded-3xl overflow-hidden border border-gray-800 transition-all duration-300 hover:-translate-y-2 hover:border-gray-600 hover:shadow-2xl hover:shadow-blue-900/20">
                    
                    <div class="h-64 overflow-hidden bg-gray-800">
                        <?php 
                            // 判斷：如果有資料庫圖就用資料庫的，沒有就用預設圖
                            $coverImage = (!empty($post['cover_image'])) 
                            ? '/' . htmlspecialchars($post['cover_image']) 
                            : $config['default_img'];
                        ?>
                        <img src="<?= $coverImage ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"  alt="<?= htmlspecialchars($post['title']) ?>">
                    </div>
                    
                    <div class="p-8 flex flex-col flex-1">
                        <h2 class="text-2xl font-bold text-white mb-4 line-clamp-2 leading-tight">
                            <?= htmlspecialchars($post['title']) ?>
                        </h2>
                        
                        <p class="text-gray-400 text-base mb-8 flex-1 leading-relaxed">
                            <?= mb_substr(strip_tags($post['content']), 0, 70) ?>...
                        </p>
                        
                        <div class="pt-6 border-t border-gray-800">
                            <a href="/post/<?= $post['id'] ?>/<?= $post['slug'] ?>"  
                               class="text-blue-400 hover:text-blue-300 font-semibold inline-flex items-center group">
                                閱讀更多 
                                <span class="ml-2 transition-transform group-hover:translate-x-1">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="mt-20 flex justify-center items-center space-x-3">
            <?php if ($page > 1): ?>
                <a href="/category/<?= $type ?>/page/<?= $page - 1 ?>" class="px-4 py-2 text-gray-400 hover:text-white transition">← 上一頁</a>
            <?php endif; ?>

            <div class="flex space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/category/<?= $type ?>/page/<?= $i ?>" 
                       class="w-12 h-12 flex items-center justify-center rounded-xl transition-all <?= $i === $page ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="/category/<?= $type ?>/page/<?= $page + 1 ?>" class="px-4 py-2 text-gray-400 hover:text-white transition">下一頁 →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
require 'footer.php'; 
?>