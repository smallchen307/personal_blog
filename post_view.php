<?php
//post_view.php

require 'db.php';                   // 引入資料庫連接設定
require_once 'Parsedown.php';       // 引入 Markdown 解析器庫
require_once 'models/Comment.php';  //引入抓取留言模組

// --- 1. 獲取並驗證文章代稱 (id) ---

$id = $_GET['id'] ?? null;  // 從 GET 請求中獲取 'id' 參數，如果不存在則為 null

// 如果 id 不存在，表示未指定要查看的文章，將用戶重定向到首頁並終止腳本執行
if (!$id) {
    header('Location: index.php');
    exit;
}

// --- 2. 從資料庫中根據 id 撈取文章資料 ---
// 準備 SQL 查詢語句，選擇 id 匹配且狀態為 'published' 的文章
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
$stmt->execute([$id]);// 執行查詢，將 $id 綁定到佔位符
$post = $stmt->fetch();// 以關聯數組的形式獲取查詢結果

// 如果查詢結果為空，表示找不到對應的文章或文章未發布，將用戶重定向到首頁並終止腳本執行
if (!$post) {
    header('Location: index.php');
    exit;
}




// --- 3. 初始化 Markdown 解析器 ---

$parsedown = new Parsedown();   // 創建 Parsedown 類別的實例
// 關閉安全模式，允許在 Markdown 中使用原始 HTML 標籤
// 注意：僅在信任內容來源時使用，以避免 XSS 風險
$parsedown->setSafeMode(false);

// --- 4. 拿到文章資料後，再撈留言

$commentModel = new Comment($pdo);
$allComments = $commentModel->getCommentsByPostId($post['id']);

// --- 5. 定義遞迴顯示函式
function renderComments($comments, $parentId = 0, $depth = 0) {
    $maxDepth = 2; // 設定最大縮排深度
    foreach ($comments as $comment) {
            // 如果這則留言的 parent_id 符合我們現在要找的層級
        if ($comment['parent_id'] == $parentId) {
                $marginClass = ($depth > 0) ? "ml-12 border-l-2 border-gray-700 pl-4 mt-4" : "mt-8";

                echo '<div class="comment-item ' . $marginClass . '">';
                echo '  <div class="flex justify-between items-center mb-2">';
                echo '    <span class="font-bold text-blue-400">' . htmlspecialchars($comment['author_name']) . '</span>';
                echo '    <span class="text-xs text-gray-500">' . $comment['created_at'] . '</span>';
                echo '  </div>';
                echo '  <div class="text-gray-700 mb-2">' . nl2br(htmlspecialchars($comment['content'])) . '</div>';
                
                echo '  <div class="text-sm">';
    
                // --- 核心修改：判斷層數 ---
                // 只有當深度小於 2 時，才顯示回覆按鈕 (0, 1 可以回覆，2 是最後一層不能再回覆)
                if ($depth < 2) {
                    echo '    <button onclick="openReplyForm(' . $comment['id'] . ', \'' . htmlspecialchars($comment['author_name']) . '\')" class="text-gray-500 hover:text-white mr-4">回覆</button>';
                } else {
                    // 可選：顯示一個提示標籤，或是直接留空
                    echo '    <span class="text-gray-600 italic mr-4">已達回覆深度上限</span>';
                }
 
                // 如果是管理員，顯示刪除按鈕
                if (isset($_SESSION['admin_id'])) {
                    echo '    <button onclick="deleteComment(' . $comment['id'] . ')" class="text-red-500/70 hover:text-red-500">刪除</button>';
                }
                echo '  </div>';

                // --- 遞迴開始 ---
                // 呼叫自己，去找 parent_id 等於現在這則留言 id 的留言
                renderComments($comments, $comment['id'], $depth + 1);
                // --- 遞迴結束 ---

                echo '</div>';
        }
    }
}

// --- 6. 設定頁面標題並載入共用頁首 ---
$pageTitle = $post['title'];// 將文章標題設定為頁面標題


// 引入 'header.php' 檔案，顯示頁面頭部內容
require 'header.php';

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
    
    /* 讓深層留言的背景稍微暗一點點，區分層次 */
    .comment-item .comment-item {
    background-color: rgba(255, 255, 255, 0.02);
    border-radius: 8px;
}
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
                <img src="/<?= htmlspecialchars($post['cover_image']) ?>" 
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

        <!--留言顯示區-->
        <div class="max-w-4xl mx-auto px-4 mt-12 mb-20">
            <h3 class="text-2xl font-bold text-white mb-6 border-b border-gray-800 pb-2">留言交流</h3>
    
            <div id="comments-display-area">
                <?php 
                if (empty($allComments)) {
                    echo '<p class="text-gray-500 italic">目前還沒有留言...</p>';
                } else {
                    renderComments($allComments); 
                }
                ?>
            </div>
        </div>

        <!--留言功能-->
        <div class="max-w-4xl mx-auto px-4 mt-12">
            <h4 id="reply-title" class="text-white font-bold mb-4">發表留言</h4>
            
            <div id="reply-info" class="hidden bg-gray-800 p-2 mb-4 rounded flex justify-between items-center">
                <span id="reply-text" class="text-blue-400 text-sm"></span>
                <button onclick="cancelReply()" class="text-red-400 text-xs hover:underline">取消回覆</button>
            </div>

            <form id="comment-form" class="space-y-4">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="hidden" name="parent_id" id="form-parent-id" value="0">
                
                <!--身分輸入區-->
                <div class="mb-4">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <label class="text-xs text-gray-500 mb-1 block">登入身份</label>
                        <input type="text" name="author_name" 
                            value="<?= htmlspecialchars($_SESSION['admin_user'] ?? '管理者') ?>" 
                            readonly 
                            class="w-full bg-gray-800 border border-gray-700 rounded p-2 text-blue-400 cursor-not-allowed">
                    <?php else: ?>
                        <label class="text-xs text-gray-500 mb-1 block">您的暱稱</label>
                        <input type="text" name="author_name" placeholder="請輸入暱稱" required 
                            class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white">
                    <?php endif; ?>
                </div>
                
                <!--留言內容輸入區-->
                <textarea name="content" rows="4" placeholder="請輸入留言內容..." required 
                        class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white"></textarea>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded shadow-lg transition">
                    送出留言
                </button>
            </form>
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

<!--留言刪除功能-->
<script>
    async function deleteComment(commentId) {
        if (!confirm('確定要刪除此留言嗎？（相關的回覆也會一併刪除）')) return;

        const formData = new FormData();
        formData.append('id', commentId);

        const response = await fetch('/controllers/CommentDeleteController.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message);
        }
    }
</script>
<!--留言功能-->
<script>
    // 點擊「回覆」按鈕時觸發
    function openReplyForm(commentId, authorName) {
        document.getElementById('form-parent-id').value = commentId;
        document.getElementById('reply-text').innerText = "正在回覆：" + authorName;
        document.getElementById('reply-info').classList.remove('hidden');
        document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth' });
    }

    // 取消回覆，回到最頂層
    function cancelReply() {
        document.getElementById('form-parent-id').value = "0";
        document.getElementById('reply-info').classList.add('hidden');
    }

    // AJAX 送出留言
    document.getElementById('comment-form').onsubmit = async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
    
        // 這裡要對準你剛才建立的 Controller 路徑
        const response = await fetch('/controllers/CommentController.php', {
            method: 'POST',
            body: formData
        });
    
        const result = await response.json();
        if (result.success) {
            location.reload(); // 成功就刷新頁面看成果
        } else {
            alert(result.message);
        }
    };
</script>


<?php 
// 引入 'footer.php' 檔案，顯示頁面底部內容
require 'footer.php'; 
?>