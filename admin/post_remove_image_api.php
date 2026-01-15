<?php
header('Content-Type: application/json');
require '../auth_check.php';
require '../db.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => '缺少 ID']);
    exit;
}

try {
    // 1. 查出舊路徑
    $stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if ($post && $post['cover_image']) {
        // 2. 刪除實體檔案
        $filePath = '../' . $post['cover_image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // 3. 更新資料庫設為 NULL
        $update = $pdo->prepare("UPDATE posts SET cover_image = NULL WHERE id = ?");
        $update->execute([$id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => '該文章本來就沒有圖片']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '刪除出錯']);
}