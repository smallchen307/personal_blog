<?php
// controllers/CommentDeleteController.php
require_once '../db.php';
session_start();

header('Content-Type: application/json');

// 權限檢查：只有登入者（管理員）可以刪除
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => '權限不足']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['id'] ?? null;

    if ($comment_id) {
        // 執行刪除（這裡建議連同子留言一起刪除，或者只刪除該筆）
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? OR parent_id = ?");
        if ($stmt->execute([$comment_id, $comment_id])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '刪除失敗']);
        }
    }
}