<?php
//設定回傳格式給瀏覽器
header('Content-Type: application/json');

//載入權限和資料庫連線
require '../auth_check.php';
require '../db.php';

try{
    //接收資料庫資料
    $id     = $_POST['id'];
    $title  = $_POST['title'];
    $slug   = $_POST['slug'];
    $type   = $_POST['type'];
    $status = $_POST['status'];
    $content = $_POST['content'];
    $image   = null;
    $stmt_old = $pdo->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $stmt_old->execute([$id]);
    $old_post = $stmt_old->fetch();
    $old_image_path = $old_post['cover_image'] ?? null;

    //圖片處理
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {//檢查檔案上傳
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];  //暫存位置
        $fileName = $_FILES['cover_image']['name'];         //原始檔名
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); //原始副檔名

        //限制副檔名
        $allowedExts = ['jpg','jpeg','png','webp'];
        
        if (in_array($fileExt,$allowedExts)) { //驗證副檔名
            $newFileName = md5(time().$fileName) . '.' . $fileExt; //重新命名檔案
            
            //驗證真的圖片檔           
            $imageInfo = getimagesize($fileTmpPath);
            if ($imageInfo === false) {
                echo json_encode(['success' => false, 'message' => '檔案不是有效的圖片']);
                exit();
            }

            $maxSize = 25 * 1024 * 1024; // 5MB
            if ($_FILES['cover_image']['size'] > $maxSize) {
                echo json_encode(['success' => false, 'message' => '檔案過大(限制25MB)']);
                exit();
            }

            $uploadDir = './uploads/'; //設定要放圖片的資料夾
            if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true); //如果資料夾不存在就建立

            $destPath = $uploadDir.$newFileName;
            if(move_uploaded_file($fileTmpPath,$destPath)){
                $imagePath = './uploads/'.$newFileName;
                if ($old_image_path && file_exists('./' . $old_image_path)) {
                unlink('./' . $old_image_path); 
            }
            }else {
                echo json_encode(['success' => false, 'message' => '檔案上傳失敗']);
                exit;
            }

        }
    }

    //資料庫對應

    // 1. 檢查是否有欄位為空
    if (!$id || empty($title) || empty($slug) || empty($type) || empty($status) || empty($content)) {
            echo json_encode(['success' => false, 'message' => '所有欄位都是必填的']);
            exit();
        }

    // 2. 根據是否有新圖片，準備不同的 SQL 與 參數陣列
    if (isset($imagePath)) {
        // 有新圖片：更新所有欄位，包含 cover_image
        $sql = "UPDATE posts SET
                    title = ?,
                    slug = ?,
                    type = ?,
                    status = ?,
                    content = ?,
                    cover_image = ?,
                    updated_at = NOW()
                WHERE id = ?";
        $params = [$title, $slug, $type, $status, $content, $imagePath, $id];
    } else {
        // 沒新圖片：不更動 cover_image 欄位
        $sql = "UPDATE posts SET
                    title = ?,
                    slug = ?,
                    type = ?,
                    status = ?,
                    content = ?,
                    updated_at = NOW()
                WHERE id = ?";
        $params = [$title, $slug, $type, $status, $content, $id];
    }

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    //回傳結果
    if($result) {
        echo json_encode(['success' => true,'message' => '文章更新成功']);
    }else {
        echo json_encode(['success' => false, 'message' => '文章更新失敗']);
    }
} catch(PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => '網址別名(Slug)已存在，請換一個']);
    } else {
        echo json_encode(['success' => false, 'message' => '資料庫錯誤：' . $e->getMessage()]);
    }
}