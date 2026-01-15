<?php
require '../auth_check.php';
require '../db.php';

// 1. 取得網址上的 ID
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: post_manager.php');
    exit;
}

// 2. 撈取該篇文章的舊資料
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// 如果找不到文章，退回列表
if (!$post) {
    header('Location: post_manager.php');
    exit;
}

require 'admin_header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">編輯內容</h1>
        <a href="post_manager.php" class="text-gray-400 hover:text-white transition">取消並返回</a>
    </div>

    <div id="errorMessage" class="hidden bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6"></div>

    <form id="editForm" class="space-y-6">
        <input type="hidden" name="id" value="<?= $post['id'] ?>">

        <div class="bg-secondary/50 p-8 rounded-2xl border border-white/10 backdrop-blur-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2">
                    <label class="block text-gray-400 mb-2">標題</label>
                    <input type="text" name="title" id="titleInput" required
                           class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"
                           value="<?= htmlspecialchars($post['title']) ?>">
                </div>

                <div>
                    <label class="block text-gray-400 mb-2">網址別名 (Slug)</label>
                    <input type="text" name="slug" id="slugInput" required
                           class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"
                           value="<?= htmlspecialchars($post['slug']) ?>">
                </div>

                <div>
                    <label class="block text-gray-400 mb-2">內容類型</label>
                    <select name="type" class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="blog" <?= $post['type'] === 'blog' ? 'selected' : '' ?>>學習日誌</option>
                        <option value="photo" <?= $post['type'] === 'photo' ? 'selected' : '' ?>>攝影作品</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-400 mb-2">發布狀態</label>
                    <select name="status" class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>草稿</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>正式發布</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-400 mb-2">內文</label>
                    <textarea name="content" rows="10" required
                              class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition"><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-400 mb-2">封面圖片</label>
                
                <?php if (!empty($post['cover_image'])): ?>
                    <div id="imagePreviewContainer" class="relative w-full max-w-sm mb-4 group">
                        <img src="../<?= htmlspecialchars($post['cover_image']) ?>" 
                            class="w-full h-48 object-cover rounded-xl border border-white/10 shadow-lg"
                            id="currentCoverImage">
                        
                        <button type="button" onclick="removeImage(<?= $post['id'] ?>)"
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>

                <input type="file" name="cover_image" accept="image/*"
                    class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
                <p class="text-xs text-gray-500 mt-2">若不更換圖片請留空；若要刪除現有圖片請點擊預覽圖右上角按鈕。</p>
            </div>
            <div class="mt-8">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl transition shadow-lg shadow-blue-600/20">
                    儲存修改
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // 3. AJAX 送出邏輯
    const editForm = document.getElementById('editForm');
    
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(editForm);
        
        try {
            const res = await fetch('post_update_api.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            
            if (data.success) {
                alert('修改成功！');
                window.location.href = 'post_manager.php';
            } else {
                showError(data.message);
            }
        } catch (e) {
            showError('伺服器連線失敗');
        }
    });

    function showError(msg) {
        const errBox = document.getElementById('errorMessage');
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    const titleInput = document.getElementById('titleInput');
    const slugInput = document.getElementById('slugInput');

    titleInput.addEventListener('input', () => {
    // 只有當 Slug 欄位目前是空的時候，才自動同步 (或者你可以決定每次都同步)
    const autoSlug = titleInput.value
        .toLowerCase()
        .replace(/[^\w\s-\u4e00-\u9fa5]/g, '') 
        .replace(/\s+/g, '-') 
        .slice(0, 50);
    
    slugInput.value = autoSlug;
    });

    //刪除圖片邏輯
    async function removeImage(id) {
        if (!confirm('確定要移除此封面圖片嗎？')) return;

        try {
            const res = await fetch('post_remove_image_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });
            const data = await res.json();

            if (data.success) {
                // 成功後，直接把前端的預覽容器藏起來
                document.getElementById('imagePreviewContainer').remove();
                alert('圖片已移除');
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('連線失敗');
        }
    }
</script>

<?php require 'admin_footer.php'; ?>