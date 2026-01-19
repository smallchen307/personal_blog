<?php

header('Content-Type:application/JSON');

require '../auth_check.php';
require '../db.php';

try{
    //取得資料 清洗資料
    $title   = trim($_POST['title']);
    $slug    = trim($_POST['slug']);
    $content = trim($_POST['content']);
    $type    = $_POST['type'];
    $status  = $_POST['status'];
    $imagePath = null; // 預設為 null

    // 處理圖片上傳

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {//檢查檔案上傳
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];  //暫存位置
        $fileName = $_FILES['cover_image']['name'];         //原始檔名
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); //原始副檔名

        // 1. 限制副檔名
        $allowedExts = ['jpg','jpeg','png','webp'];
        if (!in_array($fileExt,$allowedExts)) { //驗證副檔名
            echo json_encode(['success' => false, 'message' => '不支援的檔案格式']);
            exit;
        }            
                
        // 2. 驗證真的圖片檔           
        $imageInfo = getimagesize($fileTmpPath);
        if ($imageInfo === false) {
            echo json_encode(['success' => false, 'message' => '檔案不是有效的圖片']);
            exit();
        }

        // 3. 限制檔案大小
        $maxSize = 25 * 1024 * 1024; // 25MB
        if ($_FILES['cover_image']['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => '檔案過大(限制25MB)']);
            exit();
        }

        // 4. 準備上傳目錄
        $uploadDir = '../uploads/'; //設定要放圖片的資料夾
        
        if(!is_dir($uploadDir)) { //如果資料夾不存在就建立
            if(!mkdir($uploadDir,0777,true)) { //嘗試建立資料夾
                echo json_encode(['success' => false, 'message' => '無法建立上傳目錄，請檢查伺服器權限']);
                exit;
            }
        }

        // 5. 搬移檔案
        $newFileName = md5(time() . $fileName) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // 成功搬移，設定要存入資料庫的路徑
            $imagePath = 'uploads/' . $newFileName;
        } else {
            // 搬移失敗（這就是你遇到 Permission denied 的地方）
            echo json_encode(['success' => false, 'message' => '檔案移動失敗，請檢查資料夾寫入權限']);
            exit; // 務必中止，不要讓程式繼續執行 INSERT
        }
    }
    
    //資料驗證
    //如果 標題空白 或是 內容空白 
    if($title === '' || $content === '') {
        throw new Exception("標題或內容不得為空");
    }

    //網址別名設定提醒 （未來可考慮自動生成）
    if ($slug === '') {
       throw new Exception("請填寫網址別名(Slug)");
    }

    // 檢查 slug 是否已經存在
    $checksql = "SELECT id FROM posts WHERE slug = ?";
    $check = $pdo -> prepare ($checksql);
    $check -> execute([$slug]);
    if ($check->fetch()) {
        throw new Exception ('該網址別名已被使用，請嘗試更換');
    }

    //執行寫入資料
    $sql ="INSERT INTO posts (title,slug,content,type,status,cover_image, created_at, updated_at) VALUES (?,?,?,?,?,?, NOW(), NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title,$slug,$content,$type,$status,$imagePath]);

    //成功後回傳
    echo json_encode([
        'success' => true,
        'message' => '文章新增成功',
        'post' =>[
            'id'    => $pdo->lastInsertId(),
            'title' => htmlspecialchars($title),
            'slug'  => htmlspecialchars($slug),  // 加上這個，前端才能做連結
            'type'  => $type,
            'status'=> $status,
            'created_at' => date('Y-m-d H:i:s')  // 加上這個，前端列表顯示才完整
        ]
    ]);
} catch (Exception $e) {
    // 8. 統一錯誤出口：無論發生什麼錯，都回傳 JSON 格式的錯誤訊息
    // 這樣 Vue/React 的 axios.catch() 就能抓到這則 message
    http_response_code(400); // 告訴前端這是一個錯誤請求
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}catch (PDOException $e) {
    // 處理 DB 層級錯誤（例如 UNIQUE 衝突）
    http_response_code(400);
    if ($e->getCode() === '23000') {
        echo json_encode(['success' => false, 'message' => '該網址別名已被使用（併發衝突）']);
    } else {
        echo json_encode(['success' => false, 'message' => '資料庫錯誤']);
    }
    exit;
}