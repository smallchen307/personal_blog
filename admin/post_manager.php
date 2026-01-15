<?php
require '../auth_check.php';
require '../db.php';
require 'admin_header.php';

// 撈取所有文章，按時間倒序排列（最新的在前面）
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="flex justify-between items-center mb-10">
    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
        <span class="w-2 h-10 bg-blue-500 rounded-full"></span>
        內容管理
    </h1>
    <div id="errorMessage" class="hidden bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
        <span id="errorText"></span>
        <button onclick="document.getElementById('errorMessage').classList.add('hidden')" class="text-red-500 hover:text-white">&times;</button>
    </div>
    <a href="post_create.php" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-full shadow-lg shadow-blue-900/40 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        建立新內容
    </a>
</div>

<div class="grid grid-cols-1 gap-4">
    <?php if (empty($posts)): ?>
        <div class="text-center py-20 bg-secondary rounded-xl border border-dashed border-gray-700">
            <p class="text-gray-500">目前還沒有任何內容，開始寫第一篇吧！</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="bg-secondary p-5 rounded-xl border border-gray-700 hover:border-blue-500/50 transition-all duration-300 group flex justify-between items-center">
                <div class="flex gap-4 items-center">
                    <div class="w-16 h-12 rounded-lg bg-gray-800 flex items-center justify-center overflow-hidden border border-white/10 shadow-inner">
                        <?php if (!empty($post['cover_image'])): ?>
                            <img src="../<?= htmlspecialchars($post['cover_image']) ?>" 
                                class="w-full h-full object-cover" 
                                alt="Cover">
                        <?php else: ?>
                            <span class="text-xl">
                                <?= $post['type'] === 'photo' ? '📸' : '✍️' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-lg font-semibold text-gray-100 group-hover:text-blue-400 transition"><?= htmlspecialchars($post['title']) ?></h3>
                            <?php if ($post['status'] === 'published'): ?>
                                <span class="px-2 py-0.5 text-xs bg-green-500/20 text-green-400 border border-green-500/30 rounded">已發布</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 text-xs bg-gray-700 text-gray-400 border border-gray-600 rounded">草稿</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-500 font-mono">/post/<?= htmlspecialchars($post['slug']) ?></p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button onclick="editPost(<?= $post['id'] ?>)" class="p-2 hover:bg-gray-700 rounded-lg transition text-gray-400 hover:text-blue-400" title="編輯">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button onclick="deletePost(<?= $post['id'] ?>)" class="p-2 hover:bg-gray-700 rounded-lg transition text-gray-400 hover:text-red-400" title="刪除">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function editPost(id) {
        // 未來我們會實作 post_edit.php
        window.location.href = `post_edit.php?id=${id}`;
    }

    async function deletePost(id) {
        if (!confirm('確定要刪除這篇文章嗎？此動作無法復原。')) return;

        // 清除舊的錯誤
        const errorBox = document.getElementById('errorMessage');
        errorBox.classList.add('hidden');

        try {
            const res = await fetch('post_delete_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });
            
            const data = await res.json();
            
            if (data.success) {
                location.reload(); 
            } else {
                // 轉由統一錯誤函式顯示
                showError(data.message || '刪除失敗');
            }
        } catch (e) {
            // ✅ 這裡也換掉
            showError('連線失敗，請檢查網路');
        }
    }

    // 轉由統一錯誤函式顯示
    function showError(message) {
        const errorBox = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        errorText.textContent = message;
        errorBox.classList.remove('hidden');
        // 自動捲動到最上方讓使用者看到錯誤
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

<?php require 'admin_footer.php'; ?>