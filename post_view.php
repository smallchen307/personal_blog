<?php
// 引入資料庫連接設定
require 'db.php';
// 引入 Markdown 解析器庫
require_once 'Parsedown.php'; 

// --- 1. 獲取並驗證文章代稱 (slug) ---
// 從 GET 請求中獲取 'slug' 參數，如果不存在則為 null
$slug = $_GET['slug'] ?? null;

// 如果 slug 不存在，表示未指定要查看的文章
// 將用戶重定向到首頁並終止腳本執行
if (!$slug) {
    header('Location: index.php');
    exit;
}

// --- 2. 從資料庫中根據 slug 撈取文章資料 ---
// 準備 SQL 查詢語句，選擇 slug 匹配且狀態為 'published' 的文章
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
// 執行查詢，將 $slug 綁定到佔位符
$stmt->execute([$slug]);
// 以關聯數組的形式獲取查詢結果
$post = $stmt->fetch();

// 如果查詢結果為空，表示找不到對應的文章或文章未發布
// 將用戶重定向到首頁並終止腳本執行
if (!$post) {
    header('Location: index.php');
    exit;
}

// --- 3. 設定頁面標題並載入共用頁首 ---
// 將文章標題設定為頁面標題
$pageTitle = $post['title'];
// 引入 'header.php' 檔案，顯示頁面頭部內容
require 'header.php';



// --- 4. 初始化 Markdown 解析器 ---
// 創建 Parsedown 類別的實例
$parsedown = new Parsedown();
// 關閉安全模式，允許在 Markdown 中使用原始 HTML 標籤
// 注意：僅在信任內容來源時使用，以避免 XSS 風險
$parsedown->setSafeMode(false);
?>

<!-- 加載css額外設定 -->
<style>
    /* 專門針對文章內文容器的樣式 */
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.75rem;
        margin: 2rem auto; /* 水平居中 */
        display: block;    /* 讓 margin: auto 生效 */
        box-shadow: 0 4px 20px rgba(0,0,0,0.3); /* 加點陰影更好看 */
    }
    
    /* 你也可以順便美化 Markdown 的標題間距 */
    .post-content h2 { margin-top: 2rem; margin-bottom: 1rem; color: #fff; font-weight: bold; }
    .post-content p { line-height: 1.8; margin-bottom: 1.25rem; color: #d1d5db; }
</style>

<!-- 主要內容區域 -->
<main class="container mx-auto py-12 px-4 max-w-4xl">
    <!-- 文章容器 -->
    <article class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-gray-100">
        <!-- 文章標頭 -->
        <header class="mb-8 border-b border-gray-100 pb-8">
            <!-- 文章標題 -->
            <h1 class="text-4xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($post['title']) ?></h1>
            <!-- 文章元數據（類型、發布日期） -->
            <div class="flex items-center text-gray-400 text-sm gap-4">
                <!-- 文章類型標籤 -->
                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-medium uppercase">
                    <?= htmlspecialchars($post['type']) ?>
                </span>
                <!-- 發布日期 -->
                <time><?= date('F j, Y', strtotime($post['created_at'])) ?></time>
            </div>
        </header>

        <!-- 封面圖片 -->
        <?php if (!empty($post['cover_image'])): ?>
            <div class="mb-8 overflow-hidden rounded-xl shadow-lg">
                <img src="<?= htmlspecialchars($post['cover_image']) ?>" 
                     class="w-full h-auto object-cover" 
                     alt="Cover Image">
            </div>
        <?php endif; ?>

        <!-- 文章內容區域 -->
        <div class="markdown-content prose prose-slate max-w-none leading-relaxed text-gray-700">
            <?php 
                // 使用 Parsedown 將從資料庫取出的 Markdown 格式內容轉換為 HTML
                echo $parsedown->text($post['content']); 
            ?>
        </div>
    </article>
</main>

<!-- 引入 Prism.js 用於代碼高亮 -->
<!-- Prism.js 的 CSS 主題文件 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<!-- Prism.js 核心庫 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<!-- Prism.js 的 PHP 語言高亮組件 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<!-- Prism.js 的 JavaScript 語言高亮組件 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>

<?php 
// 引入 'footer.php' 檔案，顯示頁面底部內容
require 'footer.php'; 
?>