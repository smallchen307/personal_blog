<?php
// 在開發階段開啟錯誤報告，上線後應關閉
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 資料庫設定變送
$host = 'localhost';
$db   = 'pdo_test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; // 建議使用 utf8mb4 支援更多字元

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 修正後的連線邏輯
try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 讓錯誤以 Exception 形式拋出
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 預設使用關聯陣列
        PDO::ATTR_EMULATE_PREPARES   => false,                  // 禁用模擬預處理，增加安全性
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
} catch (PDOException $e) {
    // 捕捉連線錯誤，不顯示具體 $e->getMessage() 以免外洩密碼
    // 您可以將錯誤記錄在伺服器日誌中，但畫面上只給一個友善提示
    die("資料庫連線失敗，請稍後再試。"); 
}
?>