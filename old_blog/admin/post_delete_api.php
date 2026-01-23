<?php

//設定回傳格式給瀏覽器
header('Content-Type: application/json');

//載入權限和資料庫連線
require '../auth_check.php';
require '../db.php';

//取得前端傳回的ID
$id = $_POST['id'] ?? null;

//安全檢查
if(!$id){
    echo json_encode(['success' => false, 'message' => '缺少必要的id']);
    exit;
}

try{
    $imgstmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $imgstmt->execute([$id]);
    $imgrow = $imgstmt->fetch();

    if ($imgrow['cover_image'] && file_exists('./' . $imgrow['cover_image'])) {
        unlink('./' . $imgrow['cover_image']);
    }

    //執行刪除指令
    $delsql = "DELETE FROM posts WHERE id = ?";
    $stmt = $pdo -> prepare($delsql);
    $result = $stmt -> execute([$id]);

    if($result){
        echo json_encode(['success'=>true]);
    }else{
        echo json_encode(['success'=>false, 'message' => '刪除失敗']);
    }
} catch (PDOException $e){
    //資料庫錯誤捕捉
    echo json_encode(['success'=>false, 'message' => '資料庫錯誤: ' . $e->getMessage()]);
}