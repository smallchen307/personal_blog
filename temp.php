<?php
// controllers/CommentController.php

session_start();
require_once '../db.php';
require_once '../models/Comment.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentModel = new Comment($pdo);

    $post_id   = $_POST['post_id'] ?? null;
    $parent_id = (int)($_POST['parent_id'] ?? 0); // 強制轉轉型為整數
    $content   = trim($_POST['content'] ?? '');

    // 1. 層數硬性限制檢查 (限制只能到第三層)
    if ($parent_id > 0) {
        // 檢查「父留言」
        $stmt = $pdo->prepare("SELECT parent_id FROM comments WHERE id = ?");
        $stmt->execute([$parent_id]);
        $parent = $stmt->fetch();

        if ($parent) {
            // 如果父留言的 parent_id 不是 NULL，代表父留言在第二層
            // 那麼現在這則回覆就是第三層 (允許)
            // 但如果父留言的 parent_id 的那個留言，本身還有 parent_id，那就是第四層 (拒絕)
            if ($parent['parent_id'] !== null) {
                $stmt2 = $pdo->prepare("SELECT parent_id FROM comments WHERE id = ?");
                $stmt2->execute([$parent['parent_id']]);
                $grandParent = $stmt2->fetch();

                if ($grandParent && $grandParent['parent_id'] !== null) {
                    echo json_encode(['success' => false, 'message' => '此留言已達回覆深度上限，不允許繼續回覆']);
                    exit;
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => '回覆的對象不存在']);
            exit;
        }
    }

    // 2. 身份邏輯
    if (isset($_SESSION['admin_id'])) {
        $author = $_SESSION['admin_user'] ?? '管理者';
    } else {
        $author = htmlspecialchars(trim($_POST['author_name'] ?? '匿名訪客'));
        if (empty($author)) $author = '匿名訪客';
    }

    // 3. 執行寫入
    if ($post_id && !empty($content)) {
        if ($commentModel->createComments($post_id, $parent_id, $author, $content)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => '留言存入失敗']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '內容不可空白']);
    }
}