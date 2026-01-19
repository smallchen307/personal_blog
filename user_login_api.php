<?php
session_start();
require 'db.php';

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

// 從資料庫找這個帳號
$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = $pdo->prepare($sql);    
$stmt->execute([$user]);
$admin = $stmt->fetch();

// 驗證
if ($admin && password_verify($pass, $admin['password'])) {
    // 登入成功
    $_SESSION['admin_id'] = $admin['id'];
    header("Location: index.php");
    exit;
} else {
    // 登入失敗，導回登入頁面並顯示錯誤訊息
    header("Location: user_login.php?error=1");
}