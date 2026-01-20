<?php 
require '../auth_check.php'; 
require 'admin_header.php'; // 引入新的 Header
?>

<div class="max-w-4xl mx-auto bg-secondary rounded-xl shadow-2xl p-8 border border-gray-700">
    <h1 class="text-2xl font-bold mb-8 text-white flex items-center gap-2">
        <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
        建立新內容
    </h1>

    <!-- 新增錯誤訊息區塊 -->
    <div id="errorMessage" class="hidden bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
        <span id="errorText"></span>
    </div>

    <form id="postForm" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">內容標題＊</label>
            <input type="text" id="title" name="title" placeholder="例如：合歡山星空攝影心得" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none transition">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">網址別名 (Slug)＊</label>
            <input type="text" id="slug" name="slug" required
                   class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none transition font-mono text-blue-300 text-sm">
            <p class="text-xs text-gray-500 mt-2">預覽網址：domain.com/post/<span id="slug-preview" class="text-blue-400">...</span></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">內容類型</label>
                <select name="type" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="blog">學習日誌 (Blog)</option>
                    <option value="photo">攝影作品 (Photo)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">發布狀態</label>
                <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft">保存為草稿</option>
                    <option value="published">立即發布</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-1">詳細內容＊</label>
            <textarea id="content" name="content" rows="8" required placeholder="在此輸入您的文章或攝影說明..."
                      class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-400 mb-2">封面圖片</label>
            <input type="file" name="cover_image" accept="image/*" class="w-full bg-primary border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition">
        </div>

        <div class="flex justify-end items-center gap-6 pt-6 border-t border-gray-700">
            <a href="posts_manager.php" class="text-gray-400 hover:text-white transition text-sm">取消</a>
            <button id="submitBtn" type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-3 rounded-lg shadow-lg shadow-blue-900/20 transition duration-300">
                確認儲存內容
            </button>
        </div>
    </form>


</div>

<script>
    // 取得 DOM 元素
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');
    
    const contentInput = document.getElementById('content');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const submitBtn = document.getElementById('submitBtn');
    
    



    //Slug 轉換邏輯
    titleInput.addEventListener('input', () => {    //監聽輸入事件，使用者每次輸入時觸發，即時更新 slug
        const autoSlug = titleInput.value
            
            .trim()                                 //這裡我們保留了中文，只剔除會破壞 URL 結構的符號
            .replace(/[?#\/&?=#+.]/g, '')           // 移除 URL 中的危險保留字元：? # / & = + 
            .replace(/\s+/g, '-')                   //將一個或多個空格替換成單一連字號
            .slice(0, 100)                           //限制長度為100個字元
        slugInput.value = autoSlug;
        slugPreview.textContent = autoSlug|| '...';       //slugPreview.textContent = autoSlug || '...';
    });

    //表單送出處理
    document.getElementById('postForm').addEventListener('submit', async (e) => {
        e.preventDefault();                                 //阻止表單的預設提交行為，防止頁面重新載入 
        const formData = new FormData(e.target);            //收集表單資料

    //資料防漏填邏輯
    if (!titleInput.value.trim() || !slugInput.value.trim() || !contentInput.value.trim()) {
    showError('請填寫所有必填欄位（標題、網址別名、內容）');
    return;
    }

    // 按鈕狀態控制（新增）
    submitBtn.disabled = true;
    submitBtn.textContent = '儲存中...';
    errorMessage.classList.add('hidden');



        try {
            const res = await fetch('post_add_api.php', {   //發送 POST 請求
                method: 'POST',
                body: formData
            });
        /*    const data = await res.json();                  //解析 JSON 回應

            if (data.success) {
                alert('新增成功！');                          //成功：顯示成功訊息並導向 posts_manager.php 管理頁面
                window.location.href = 'post_manager.php'; 
            } else {
                alert('錯誤：' + data.message);               //顯示錯誤訊息
            }
        */
            if (!res.ok) {  // ← 新增 HTTP 狀態檢查
            const data = await res.json();
            throw new Error(data.message || '伺服器回應錯誤');
            }
    
            const data = await res.json();
            if (data.success) {
                window.location.href = 'post_manager.php';  // ← 移除 alert 直接跳轉
            } else {
                showError(data.message || '新增失敗，請稍後再試');  // ← 改用函式
            }

        } catch (error) {
            showError(error.message || '連線伺服器失敗，請檢查網路');  // ← 改用函式
        } finally {  // ← 新增 finally 恢復按鈕
            submitBtn.disabled = false;
            submitBtn.textContent = '確認儲存內容';
}
    });


    // ✅ 定義 showError 函式
    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

<?php require 'admin_footer.php'; ?>