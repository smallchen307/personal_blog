<!DOCTYPE html>
<html lang="zh-han">
<head>
    <meta charset="UTF-8">
    <title>使用者管理系統</title>
</head>
<body>
    <nav>
        <a href="index.php">首頁</a>
        <?php if(isset($_SESSION['admin_id'])): ?>
            歡迎，<?= htmlspecialchars($_SESSION['admin_user'] ?? '管理者') ?> 
            <a href="user_logout_api.php">登出系統</a>
        <?php else: ?>
            <a href="user_login.php">登入</a>
        <?php endif; ?>
    </nav>
    <hr>