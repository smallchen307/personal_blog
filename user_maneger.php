
<?php
require 'auth_check.php';
require 'db.php'; 
require 'header.php';


$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>新增使用者</h2>
<form id="addForm" method="POST">
    <input type="text" name="name" placeholder="姓名" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="number" name="age" placeholder="年齡" required>
    <button type="submit">新增</button>
</form>

<p id="addMsg"></p>

<hr>

<h2>使用者列表</h2>
<div id="userlist">
<?php foreach ($users as $user): ?>
    <div class="user" id="user-<?= $user['id'] ?>" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['name']) ?>" data-email="<?= htmlspecialchars($user['email']) ?>" data-age="<?= $user['age'] ?>">
        <span class="display_text">
            <?= $user['id'] ?> /
            <?= htmlspecialchars($user['name']) ?> /
            <?= htmlspecialchars($user['email']) ?> /
            <?= $user['age'] ?>
        </span>
        <div class='action_buttons inline'>
            <!-- 刪除按鈕（AJAX，不跳頁） -->
            <button onclick="deleteUser(<?= $user['id'] ?>)">刪除</button>
            <!-- 編輯按鈕（原本 GET 送 id） -->
            <button onclick="editUser(<?= $user['id'] ?>)">編輯</button>
        </div>
    </div>

<?php endforeach; ?>
</div>

<?php include 'footer.php'; // 引入頁尾 ?>
<script>
        //刪除使用者
        function deleteUser(id) { 
            if (!confirm('確定要刪除 ID ' + id + ' 嗎？')) return;

            const formData = new FormData();
            formData.append('id', id);

            fetch('user_delete_api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('user-' + id).remove();
                } else {
                    alert('刪除失敗：' + (data.message || '未知錯誤'));
                }
            });
        }

        //新增使用者
        const addForm = document.getElementById('addForm');
        addForm.addEventListener('submit',e=> {
            e.preventDefault(); //阻止跳頁
            const formData = new FormData(addForm);
            //發送請求
            fetch('user_add_api.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                const msg = document.getElementById('addMsg');

                if(!data.success){
                    msg.textContent = data.message;
                    msg.style.color = 'red';
                    return;
                }

                msg.textContent = '新增成功';
                msg.style.color = 'green';
                
                //把新增的使用者加入列表
                const userList = document.getElementById('userlist');
                const user = data.user;
                const div = document.createElement('div');
                div.className = 'user';
                div.id = 'user-' + user.id;

                // 核心修正：同步設定 data-* 屬性，這樣新產生的這一列才能被 editUser() 抓到資料
                div.dataset.id = user.id;
                div.dataset.name = user.name;
                div.dataset.email = user.email;
                div.dataset.age = user.age;

                div.innerHTML = `
                    <span class="display_text">
                        ${user.id} / ${user.name} / ${user.email} / ${user.age}
                    </span>
                    <div class='action_buttons inline'>
                        <button onclick="deleteUser(${user.id})">刪除</button>
                        <button onclick="editUser(${user.id})">編輯</button>
                    </div>
                `;
                                
                userList.prepend(div);
                addForm.reset();
                
            })
        });
        
        //編輯使用者編輯模式
        function editUser(id) {
            const row = document.getElementById('user-' + id);

            //從dataset抓取資料
            const name  = row.dataset.name;
            const email = row.dataset.email;
            const age   = row.dataset.age;

            row.dataset.oldHtml = row.innerHTML;

            row.innerHTML = `
            ${id} / 
            <input type="text" id="edit-name-${id}" value="${name}">
            <input type="email" id="edit-email-${id}" value="${email}">
            <input type="number" id="edit-age-${id}" value="${age}">
            <button onclick="updateUser(${id})">儲存</button>
            <button onclick="cancelEdit(${id})">取消</button>
            `
        }

        //編輯使用者取消模式
        function cancelEdit(id) {
            const row = document.getElementById('user-' + id);
            row.innerHTML = row.dataset.oldHtml;
        }

        //編輯使用者執行更新
        function updateUser(id) {
            const row = document.getElementById('user-' + id)
            const newName = document.getElementById(`edit-name-${id}`).value;
            const newEmail = document.getElementById(`edit-email-${id}`).value;
            const newAge = document.getElementById(`edit-age-${id}`).value;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', newName);
            formData.append('email', newEmail);
            formData.append('age', newAge);

            fetch('user_update_api.php', {
                method:'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    //更新成功  dataset更新
                    row.dataset.name = data.user.name;
                    row.dataset.email = data.user.email;
                    row.dataset.age = data.user.age;

                    // 回復顯示模式
                    row.innerHTML = `
                        <span class="display_text">
                            ${data.user.id} / ${data.user.name} / ${data.user.email} / ${data.user.age}
                        </span>
                        <div class="action-buttons inline">
                            <button onclick="deleteUser(${data.user.id})">刪除</button>
                            <button onclick="editUser(${data.user.id})">編輯</button>
                        </div>
                    `;
                }else {
                    alert('更新失敗：' + data.message);
                }
            })

        }
 </script>


</html>
