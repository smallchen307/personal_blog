<?php
require 'auth_check.php';
require 'db.php'; // 已經有 $pdo 連線

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';

if ($id === '') {
    echo json_encode([
        'success' => false,
        'message' => '沒有收到 ID'
    ]);
    exit;
}

/* === 你原本的 delete 風格，原封不動 === */
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
/* ======================================= */

if ($stmt->rowCount() === 1) {
    echo json_encode([
        'success' => true,
        'deleted_id' => $id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '刪除失敗或資料不存在'
    ]);
}
