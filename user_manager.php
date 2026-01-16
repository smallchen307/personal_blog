<?php
// 引入 `auth_check.php`：檢查用戶是否登入，如果未登入，會重定向到登入頁面。這是保護頁面的第一道防線。
require 'auth_check.php';
// 引入 `db.php`：建立與資料庫的連接。這個文件通常包含資料庫主機、用戶名、密碼和資料庫名稱等設定，並創建一個 PDO 物件。
require 'db.php'; 
// 引入 `header.php`：包含網站的共用頁首，例如 HTML 的 <head> 部分、導航欄等。
require 'header.php';


// 準備並執行一個 SQL 查詢，從 `users` 資料表中選取所有用戶資料。
// "SELECT * FROM users" 表示選取所有欄位。
// "ORDER BY id DESC" 表示將結果按 `id` 降序排列，這樣最新的用戶會先顯示。
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
// 使用 `fetchAll(PDO::FETCH_ASSOC)` 從查詢結果中獲取所有行，並將它們作為關聯數組（associative array）存儲在 `$users` 變數中。
// 關聯數組的鍵是資料表的欄位名。
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- 主內容區域開始 -->
<main class="container mx-auto py-12 px-4">
    <div class="max-w-6xl mx-auto">
        
        <!-- 新增使用者區塊的標題 -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">新增使用者</h2>
        <!-- "admin-card" 是一個自訂樣式，用於美化區塊 -->
        <div class="admin-card">
            <!-- 新增使用者的表單 -->
            <form id="addForm" class="flex flex-wrap gap-4 items-center">
                <!-- 姓名輸入框 -->
                <input type="text" name="name" placeholder="姓名" required class="flex-1 min-w-[150px]">
                <!-- Email 輸入框 -->
                <input type="email" name="email" placeholder="Email" required class="flex-1 min-w-[200px]">
                <!-- 年齡輸入框 -->
                <input type="number" name="age" placeholder="年齡" required class="w-24">
                <!-- 提交按鈕 -->
                <button type="submit" class="bg-red-500 hover:bg-red-600">新增用戶</button>
            </form>
            <!-- 用於顯示新增使用者操作結果（成功或失敗）的訊息區塊 -->
            <p id="addMsg" class="mt-4 text-sm font-medium"></p>
        </div>

        <!-- 分隔線 -->
        <hr class="my-10 border-gray-200">

        <!-- 使用者列表區塊的標題 -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">使用者列表</h2>
        <!-- 用於顯示所有使用者的容器 -->
        <div id="userlist" class="space-y-4">
            <?php // 開始 PHP 循環，遍歷從資料庫獲取的 `$users` 陣列 ?>
            <?php foreach ($users as $user): ?>
                <!-- 每個使用者的卡片，`id` 用於讓 JavaScript 能夠輕鬆找到並操作這個元素 -->
                <!-- `data-*` 屬性用於儲存該使用者的原始資料，方便 JavaScript 在編輯時讀取 -->
                <div class="admin-card flex justify-between items-center" id="user-<?= $user['id'] ?>" 
                     data-id="<?= $user['id'] ?>" 
                     data-name="<?= htmlspecialchars($user['name']) ?>" 
                     data-email="<?= htmlspecialchars($user['email']) ?>" 
                     data-age="<?= $user['age'] ?>">
                    
                    <!-- 顯示使用者資訊的區塊 -->
                    <span class="display_text font-mono text-gray-700">
                        <!-- 'htmlspecialchars()' 函數可以防止 XSS 攻擊，它會將特殊字符轉換為 HTML 實體 -->
                        <span class="bg-gray-100 px-2 py-1 rounded text-xs mr-2">ID: <?= $user['id'] ?></span>
                        <strong class="text-lg"><?= htmlspecialchars($user['name']) ?></strong> 
                        <span class="text-gray-400 mx-2">/</span> <?= htmlspecialchars($user['email']) ?> 
                        <span class="text-gray-400 mx-2">/</span> <?= $user['age'] ?> 歲
                    </span>

                    <!-- 操作按鈕的容器 -->
                    <div class='action_buttons flex gap-2'>
                        <!-- 編輯按鈕，點擊時調用 `editUser` JavaScript 函數，並傳入用戶 ID -->
                        <button onclick="editUser(<?= $user['id'] ?>)" class="bg-gray-200 !text-gray-800 hover:bg-gray-300">編輯</button>
                        <!-- 刪除按鈕，點擊時調用 `deleteUser` JavaScript 函數，並傳入用戶 ID -->
                        <button onclick="deleteUser(<?= $user['id'] ?>)" class="bg-red-100 !text-red-600 hover:bg-red-200">刪除</button>
                    </div>
                </div>
            <?php endforeach; // 結束 foreach 循環 ?>
        </div>
    </div>
</main>


<?php include 'footer.php'; // 引入共用的頁尾，通常包含 JavaScript 庫的引入和頁腳版權資訊 ?>
<script>
    // ** JavaScript 腳本區塊 **
    // 這裡的程式碼負責處理頁面上的所有互動操作，例如新增、刪除、編輯使用者，而不需要重新整理整個頁面。

    /**
     * @brief 產生並返回一個使用者資料列的 HTML 模板字符串。
     * @param {object} user - 包含 id, name, email, age 的使用者物件。
     * @returns {string} - 代表該使用者顯示行的 HTML。
     * @description 這個函數的目的是統一使用者資料的顯示樣式。
     *              無論是 PHP 首次載入頁面，還是 JavaScript 動態新增/更新，
     *              都使用這個模板來確保 HTML 結構和樣式一致。
     */
    function getRowTemplate(user) {
        return `
            <span class="display_text font-mono text-gray-700">
                <span class="bg-gray-100 px-2 py-1 rounded text-xs mr-2">ID: ${user.id}</span>
                <strong class="text-lg">${user.name}</strong> 
                <span class="text-gray-400 mx-2">/</span> ${user.email} 
                <span class="text-gray-400 mx-2">/</span> ${user.age} 歲
            </span>
            <div class='action_buttons flex gap-2'>
                <button onclick="editUser(${user.id})" class="bg-gray-200 !text-gray-800 hover:bg-gray-300 px-4 py-2 rounded-md text-sm">編輯</button>
                <button onclick="deleteUser(${user.id})" class="bg-red-100 !text-red-600 hover:bg-red-200 px-4 py-2 rounded-md text-sm">刪除</button>
            </div>
        `;
    }

    /**
     * @brief 處理刪除使用者的操作。
     * @param {number} id - 要刪除的使用者的 ID。
     * @description 當使用者點擊「刪除」按鈕時觸發。
     *              1. 顯示一個確認對話框，防止誤刪。
     *              2. 如果確認，會透過 `fetch` API 向後端 `user_delete_api.php` 發送一個 POST 請求。
     *              3. 後端處理刪除操作後，返回 JSON 格式的結果。
     *              4. 如果後端返回成功，前端會從頁面上移除對應的使用者元素。
     */
    function deleteUser(id) { 
        if (!confirm('確定要刪除 ID ' + id + ' 嗎？')) return;
        const formData = new FormData();
        formData.append('id', id);
        fetch('user_delete_api.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) document.getElementById('user-' + id).remove();
            else alert('刪除失敗');
        });
    }

    // --- 新增使用者的邏輯 ---
    const addForm = document.getElementById('addForm'); // 獲取新增表單元素
    // 為表單的 'submit' 事件添加一個監聽器
    addForm.addEventListener('submit', e => {
        e.preventDefault(); // 防止表單提交時頁面重新整理
        const formData = new FormData(addForm); // 從表單中收集所有輸入的資料
        
        // 使用 fetch API 將表單資料以 POST 方式發送到後端 'user_add_api.php'
        fetch('user_add_api.php', { method: 'POST', body: formData })
        .then(res => res.json()) // 將後端返回的 JSON 響應轉換為 JavaScript 物件
        .then(data => {
            const msg = document.getElementById('addMsg'); // 獲取用於顯示訊息的元素
            // 如果後端返回操作失敗
            if(!data.success){
                msg.textContent = data.message; // 顯示錯誤訊息
                msg.style.color = 'red'; // 將訊息文字設為紅色
                return;
            }
            // 如果後端返回操作成功
            msg.textContent = '新增成功'; // 顯示成功訊息
            msg.style.color = 'green'; // 將訊息文字設為綠色
            
            const userList = document.getElementById('userlist'); // 獲取使用者列表容器
            const user = data.user; // 從後端返回的資料中獲取新增的使用者物件
            const div = document.createElement('div'); // 創建一個新的 div 元素來顯示新使用者
            
            // 設定新 div 的屬性，使其與 PHP 渲染的元素結構一致
            div.className = 'admin-card flex justify-between items-center';
            div.id = 'user-' + user.id;
            div.dataset.id = user.id;
            div.dataset.name = user.name;
            div.dataset.email = user.email;
            div.dataset.age = user.age;

            div.innerHTML = getRowTemplate(user); // 使用統一的模板函數生成顯示內容
            userList.prepend(div); // 將新元素添加到使用者列表的最前面
            addForm.reset(); // 清空新增表單的輸入框
        });
    });
    
    /**
     * @brief 將指定的使用者行切換到「編輯模式」。
     * @param {number} id - 要編輯的使用者的 ID。
     * @description 當使用者點擊「編輯」按鈕時觸發。
     *              1. 根據 ID 找到對應的行。
     *              2. 從該行的 `data-*` 屬性讀取目前的使用者資料。
     *              3. 將該行的 `innerHTML` 保存到 `dataset.oldHtml` 中，以便取消編輯時可以還原。
     *              4. 將該行的內容替換為包含輸入框和「儲存」、「取消」按鈕的 HTML。
     */
    function editUser(id) {
        const row = document.getElementById('user-' + id);
        const name = row.dataset.name;
        const email = row.dataset.email;
        const age = row.dataset.age;

        // 保存當前的 HTML 結構，以便取消時可以還原
        row.dataset.oldHtml = row.innerHTML;

        // 替換為編輯介面
        row.innerHTML = `
            <div class="flex flex-wrap gap-2 items-center w-full">
                <span class="font-bold text-blue-600">ID: ${id}</span>
                <input type="text" id="edit-name-${id}" value="${name}" class="border p-2 rounded flex-1">
                <input type="email" id="edit-email-${id}" value="${email}" class="border p-2 rounded flex-1">
                <input type="number" id="edit-age-${id}" value="${age}" class="border p-2 rounded w-20">
                <div class="flex gap-2">
                    <button onclick="updateUser(${id})" class="bg-green-600 text-white px-4 py-2 rounded">儲存</button>
                    <button onclick="cancelEdit(${id})" class="bg-gray-400 text-white px-4 py-2 rounded">取消</button>
                </div>
            </div>
        `;
    }

    /**
     * @brief 取消編輯模式，還原該行的原始顯示狀態。
     * @param {number} id - 要取消編輯的使用者的 ID。
     * @description 當使用者在編輯模式下點擊「取消」按鈕時觸發。
     *              它會讀取之前保存在 `dataset.oldHtml` 的內容，並將其還原。
     */
    function cancelEdit(id) {
        const row = document.getElementById('user-' + id);
        row.innerHTML = row.dataset.oldHtml;
    }

    /**
     * @brief 執行更新操作，將修改後的資料發送到後端。
     * @param {number} id - 要更新的使用者的 ID。
     * @description 當使用者在編輯模式下點擊「儲存」按鈕時觸發。
     *              1. 從編輯輸入框中獲取新的姓名、Email 和年齡。
     *              2. 構建一個 `FormData` 物件來裝載這些新資料。
     *              3. 透過 `fetch` API 將資料發送到後端 `user_update_api.php`。
     *              4. 如果更新成功，後端會返回最新的使用者資料。
     *              5. 前端更新該行的 `data-*` 屬性，並使用 `getRowTemplate` 函數還原其顯示樣式。
     *              6. 如果更新失敗，則顯示錯誤訊息。
     */
    function updateUser(id) {
        const row = document.getElementById('user-' + id);
        const newName = document.getElementById(`edit-name-${id}`).value;
        const newEmail = document.getElementById(`edit-email-${id}`).value;
        const newAge = document.getElementById(`edit-age-${id}`).value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('name', newName);
        formData.append('email', newEmail);
        formData.append('age', newAge);

        fetch('user_update_api.php', { method:'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // 更新 dataset 中的資料，這樣下次再按「編輯」時就會是最新資料
                row.dataset.name = data.user.name;
                row.dataset.email = data.user.email;
                row.dataset.age = data.user.age;

                // 使用統一模板還原成漂亮的顯示樣式
                row.innerHTML = getRowTemplate(data.user);
            } else {
                alert('更新失敗：' + data.message);
            }
        });
    }
</script>


</html>