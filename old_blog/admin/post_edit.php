<?php
// 引入身份驗證檢查腳本，確保只有登入的用戶才能訪問此頁面
require '../auth_check.php';
// 引入資料庫連接腳本
require '../db.php';

// --- 資料準備 ---

// 1. 從 URL 取得文章 ID
// 使用 null 合併運算子 (??) 來避免在 'id' 未設定時產生錯誤
$id = $_GET['id'] ?? null;

// 如果 URL 中沒有提供 ID，將用戶重定向到文章管理頁面並終止腳本
if (!$id) {
    header('Location: post_manager.php');
    exit;
}

// 2. 從資料庫撈取該篇文章的現有資料
// 準備 SQL 查詢語句，使用預備語句防止 SQL 注入
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
// 執行查詢，將 ID 綁定到佔位符
$stmt->execute([$id]);
// 以關聯陣列形式獲取查詢結果
$post = $stmt->fetch();

// 如果根據 ID 找不到對應的文章，同樣重定向到文章管理頁面
if (!$post) {
    header('Location: post_manager.php');
    exit;
}

// --- 頁面渲染 ---

// 引入後台管理的頁首 HTML 結構
require 'admin_header.php';
?>

<!-- 主要內容容器 -->
<div class="max-w-4xl mx-auto">
    <!-- 頁面標題和返回按鈕 -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">編輯內容</h1>
        <a href="post_manager.php" class="text-gray-400 hover:text-white transition">取消並返回</a>
    </div>

    <!-- 錯誤訊息顯示區塊，預設隱藏 -->
    <div id="errorMessage" class="hidden bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6"></div>

    <!-- 編輯表單 -->
    <form id="editForm" class="space-y-6">
        <!-- 隱藏欄位，用於提交文章的 ID -->
        <input type="hidden" name="id" value="<?= $post['id'] ?>">

        <!-- 表單主要區塊 -->
        <div class="bg-secondary/50 p-8 rounded-2xl border border-white/10 backdrop-blur-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- 標題欄位 -->
                <div class="md:col-span-2">
                    <label class="block text-gray-400 mb-2">標題</label>
                    <input type="text" name="title" id="titleInput" required
                           class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"
                           value="<?= htmlspecialchars($post['title']) // 使用 htmlspecialchars 防止 XSS 攻擊 ?>">
                </div>

                <!-- 網址別名 (Slug) 欄位 -->
                <div>
                    <label class="block text-gray-400 mb-2">網址別名 (Slug)</label>
                    <input type="text" name="slug" id="slugInput" required
                           class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"
                           value="<?= htmlspecialchars($post['slug']) ?>">
                </div>

                <!-- 內容類型下拉選單 -->
                <div>
                    <label class="block text-gray-400 mb-2">內容類型</label>
                    <select name="type" class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="blog" <?= $post['type'] === 'blog' ? 'selected' : '' ?>>學習日誌</option>
                        <option value="photo" <?= $post['type'] === 'photo' ? 'selected' : '' ?>>攝影作品</option>
                    </select>
                </div>

                <!-- 發布狀態下拉選單 -->
                <div>
                    <label class="block text-gray-400 mb-2">發布狀態</label>
                    <select name="status" class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>草稿</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>正式發布</option>
                    </select>
                </div>

                <!-- 內文編輯區 -->
                <div class="md:col-span-2">
                    <label class="block text-gray-400 mb-2">內文 (Markdown)</label>
                    <textarea name="content" id="post_content_editor" rows="10" required class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
            </div>
            
            <!-- 封面圖片區塊 -->
            <div class="md:col-span-2 mt-6">
                <label class="block text-gray-400 mb-2">封面圖片</label>
                
                <?php // 如果文章已有封面圖片，則顯示預覽圖和刪除按鈕 ?>
                <?php if (!empty($post['cover_image'])): ?>
                    <div id="imagePreviewContainer" class="relative w-full max-w-sm mb-4 group">
                        <img src="./<?= htmlspecialchars($post['cover_image']) ?>" 
                            class="w-full h-48 object-cover rounded-xl border border-white/10 shadow-lg"
                            id="currentCoverImage">
                        
                        <!-- 刪除圖片按鈕 -->
                        <button type="button" onclick="removeImage(<?= $post['id'] ?>)"
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- 上傳新圖片的輸入欄位 -->
                <input type="file" name="cover_image" accept="image/*"
                    class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                <p class="text-xs text-gray-500 mt-2">若不更換圖片請留空；若要刪除現有圖片請點擊預覽圖右上角按鈕。</p>
            </div>

            <!-- 提交按鈕 -->
            <div class="mt-8">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20">
                    儲存修改
                </button>
            </div>
        </div>
    </form>
</div>


<!-- 引入 EasyMDE Markdown 編輯器的 CSS 和 JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>


<script>
// --- 表單與互動邏輯 ---
document.addEventListener('DOMContentLoaded', function() {
    // 初始化 EasyMDE
    const easyMDE = new EasyMDE({
        // 綁定到我們指定的 textarea
        element: document.getElementById('post_content_editor'), 
        // 關閉拼寫檢查
        spellChecker: false,
        // 設定自動儲存功能
        autosave: {
            enabled: true,
            // 使用文章 ID 建立唯一的儲存鍵值，避免不同文章的草稿互相覆蓋
            uniqueId: "post_edit_<?= $post['id'] ?>", 
            delay: 1000, // 每 1000 毫秒 (1秒) 儲存一次
        },
        // 設定工具列按鈕
        toolbar: [
            "bold", "italic", "heading", "|", 
            "quote", "unordered-list", "ordered-list", "|", 
            "link", "image", "code", "table", "|", 
            "preview", "side-by-side", "fullscreen"
        ],
        // 編輯器預設顯示的提示文字
        placeholder: "開始撰寫你的 Markdown 內容...",
        // 在狀態列顯示的資訊
        status: ["autosave", "lines", "words", "cursor"], 
    });

    // --- AJAX 表單提交 ---
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', async (e) => {
        // 防止表單的預設同步提交行為
        e.preventDefault();
        
        // 建立 FormData 物件，自動收集表單中的所有欄位資料 (包含檔案)
        const formData = new FormData(editForm);
        // 手動將 EasyMDE 編輯器的最新內容同步到 FormData 中
        // 因為 EasyMDE 的內容不會自動更新到原始的 textarea
        formData.set('content', easyMDE.value());
        
        try {
            // 使用 fetch API 以 POST 方法將表單資料非同步傳送到後端 API
            const res = await fetch('post_update_api.php', {
                method: 'POST',
                body: formData
            });
            // 解析後端返回的 JSON 格式回應
            const data = await res.json();
            
            if (data.success) {
                // 如果操作成功
                alert('修改成功！');
                // 清除此文章的自動儲存草稿
                    // 1. 先用內建方法
                easyMDE.clearAutosavedValue();
                    // 2. 額外手動清理 (smde_ 是 EasyMDE 預設的前綴詞)
                localStorage.removeItem('smde_post_edit_<?= $post['id'] ?>');
                window.location.href = 'post_manager.php';
                // 將頁面重定向到文章管理列表
                window.location.href = 'post_manager.php';
            } else {
                // 如果後端返回錯誤，顯示錯誤訊息
                showError(data.message);
            }
        } catch (e) {
            // 如果網路請求失敗或解析 JSON 出錯，顯示通用錯誤訊息
            showError('伺服器連線失敗，請檢查網路或稍後再試。');
            console.error(e); // 在控制台輸出詳細錯誤以供除錯
        }
    });



    // --- 自動生成 Slug ---
    const titleInput = document.getElementById('titleInput');
    const slugInput = document.getElementById('slugInput');

    titleInput.addEventListener('input', () => {
        // 將標題轉換為小寫
        const autoSlug = titleInput.value
            .trim()                                 //這裡我們保留了中文，只剔除會破壞 URL 結構的符號
            .replace(/[?#\/&?=#+.]/g, '')           // 移除 URL 中的危險保留字元：? # / & = + 
            .replace(/\s+/g, '-')                   //將一個或多個空格替換成單一連字號
            .slice(0, 100)                           //限制長度為100個字元
        
        slugInput.value = autoSlug;
    });

});
// --- 錯誤訊息顯示函式 ---
function showError(msg) {
    const errBox = document.getElementById('errorMessage');
    errBox.textContent = msg; // 設定錯誤訊息文字
    errBox.classList.remove('hidden'); // 移除 'hidden' class 使其可見
    // 將頁面捲動到頂部，讓用戶能立刻看到錯誤訊息
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
 
// --- 刪除圖片邏輯 ---
async function removeImage(id) {
    // 彈出確認對話框，如果用戶取消則中止函式
    if (!confirm('確定要移除此封面圖片嗎？此操作將會儲存。')) return;

    try {
        // 發送 POST 請求到刪除圖片的 API
        const res = await fetch('post_remove_image_api.php', {
            method: 'POST',
            // 設定請求標頭為表單 URL 編碼格式
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            // 在請求主體中傳遞文章 ID
            body: `id=${id}`
        });
        const data = await res.json();

        if (data.success) {
            // 如果刪除成功
            const previewContainer = document.getElementById('imagePreviewContainer');
            if (previewContainer) {
                // 從 DOM 中移除圖片預覽容器
                previewContainer.remove();
            }
            alert('圖片已移除');
        } else {
            // 如果後端返回錯誤，顯示錯誤訊息
            alert(data.message);
        }
    } catch (e) {
        // 如果網路請求失敗，顯示通用錯誤訊息
        alert('連線失敗，請稍後再試。');
        console.error(e);
    }
}
</script>

<?php 
// 引入後台管理的頁尾 HTML 結構
require 'admin_footer.php'; 
?>