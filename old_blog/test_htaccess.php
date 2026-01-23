<?php
// 測試 .htaccess 是否運作正常
$type = $_GET['type'] ?? '未偵測到分類';
$page = $_GET['page'] ?? '1 (預設值)';

echo "<h1>路徑測試成功！</h1>";
echo "你正在查看的分類是：<b>" . htmlspecialchars($type) . "</b><br>";
echo "目前的頁碼是：<b>" . htmlspecialchars($page) . "</b><br>";

echo "<hr>";
echo "目前的網址參數內容 (DEBUG)：<pre>";
print_r($_GET);
echo "</pre>";