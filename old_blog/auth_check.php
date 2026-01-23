<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
// 取得目前執行的檔案名稱
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// 只有在「不是」登入頁的時候才檢查 Session
if (!isset($_SESSION['admin_id']) && $currentPage !== 'login.php') {
    header("Location: ./user_login.php");
    exit;
}
?>