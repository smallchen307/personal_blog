<?php
require 'db.php';
// 這裡不需要引入 auth_check，因為登入頁不需要檢查是否已登入
$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
$isAdminExist = $stmt->fetchColumn() > 0;
?>

<!DOCTYPE html>
<html>
<head><title>登入</title></head>
<body>
    <?php if (!$isAdminExist): ?>
        <div style="background: #fff3cd; padding: 10px; margin-bottom: 10px;">
            偵測到系統尚未初始化，<a href="seed.php">請點擊此處建立初始管理員</a>。
        </div>
    <?php endif; ?>

    <h2>管理員登入</h2>
    <form action="user_login_api.php" method="POST">
        <input type="text" name="username" placeholder="帳號" required>
        <input type="password" name="password" placeholder="密碼" required>
        <button type="submit">登入</button>
    </form>
</body>
</html>