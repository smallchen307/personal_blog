<?php
require 'auth_check.php';
require 'db.php';

$id =  $_GET['id'] ?? '';

if ($id === '' || !is_numeric($id)) {
    echo "不合法，請重新檢查輸入的 ID";
    exit;
}

// 取得指定 ID 的使用者資料
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "找不到該使用者資料";
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title>編輯使用者</title>
    </head>
    <body>
        <h1>編輯使用者</h1>
        
        　<form action="user_update_api.php" method="post">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="姓名"><br><br>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email"><br><br>
            <input type="number" name="age" value="<?= $user['age'] ?>" placeholder="年齡"><br><br>

            <button type="submit">更新</button>
        </form>

    </body>
</html>
