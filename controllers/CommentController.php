<?php
// controllers/CommentController.php

/**
 * 評論控制器 (Comment Controller)
 * 
 * 這個檔案負責處理來自前端的評論提交請求。
 * 它會驗證接收到的資料，判斷使用者身分，
 * 並將新的評論資料存入資料庫中。
 * 最後，它會回傳一個 JSON 格式的結果給前端。
 */

// 啟動或恢復一個 session，以便存取 $_SESSION 變數，主要用來判斷使用者是否登入
session_start();


require_once '../db.php';
require_once '../models/Comment.php';

// 檢查 HTTP 請求的方法是否為 'POST'
// 確保只有透過 POST 請求提交的資料才會被處理，增加安全性
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 實例化 Comment 模型，並傳入資料庫連接物件 $pdo
    $commentModel = new Comment($pdo);
    // 從 POST 資料中獲取 'post_id'，如果不存在則設為 null
    $post_id   = $_POST['post_id'] ?? null;
    // 從 POST 資料中獲取 'parent_id' (用於回覆評論)，如果不存在則設為 0 (代表這是一則主評論)
    $parent_id = (int)$_POST['parent_id'] ?? 0; 
    // 從 POST 資料中獲取 'content'，並用 trim() 移除前後的空白字元
    $content   = trim($_POST['content'] ?? '');

// --- 1. 硬性層數限制檢查 ＝ ---
    if ($parent_id > 0) {
        // 檢查父留言的深度
        // 這裡可以使用一個簡單的 SQL 查詢來確認該 parent_id 的留言是否還能被回覆
        $checkStmt = $pdo->prepare("SELECT parent_id FROM comments WHERE id = ?");
        $checkStmt->execute([$parent_id]);
        $parent = $checkStmt->fetch();

        if ($parent) {
            // 如果父留言本身也有 parent_id (代表它是第 2 層)，
            // 那麼再回覆它就會變成第 3 層 -> 這是最後允許的
            // 如果父留言的父留言還有 parent_id -> 代表回覆會變第 4 層 -> 拒絕
            
            $grandParentId = $parent['parent_id'];
            if ($grandParentId !== null) {
                $checkGrand = $pdo->prepare("SELECT parent_id FROM comments WHERE id = ?");
                $checkGrand->execute([$grandParentId]);
                $grandParent = $checkGrand->fetch();
                
                if ($grandParent && $grandParent['parent_id'] !== null) {
                    echo json_encode(['success' => false, 'message' => '此留言已達回覆深度上限，不允許繼續回覆']);
                    exit;
                }
            }
        }
    }
// --- 2. 身份邏輯優化 ---
    if (isset($_SESSION['admin_id'])) {
        // 如果是登入狀態，強制使用 Session 裡的名稱
        $author = $_SESSION['admin_user'] ?? '管理者';
    } else {
        // 如果是訪客，才抓取填寫的名稱，如果沒填就叫匿名
        $author = htmlspecialchars(trim($_POST['author_name'] ?? '匿名訪客'));
        if (empty($author)) $author = '匿名訪客';
    }
// --- 3. 執行寫入
    if ($post_id && !empty($content)) {
        if ($commentModel->createComments($post_id, $parent_id, $author, $content)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '資料庫存入失敗']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '內容不可空白']);
    }
}


// 假設你已經接收了 $parent_id
