<?php
require 'auth_check.php';
require 'db.php';
header('Content-Type: application/json');


//取得資料
$name =  trim( $_POST['name'] ?? '');
$email = trim( $_POST['email'] ?? '');
$age =   trim($_POST['age'] ?? '');

//基本欄位檢查
if ($name === '' || $email === '' || $age === ''){
    echo json_encode(['success'=>false,'message'=>'請填寫完整資料']);
    exit;
}

//驗證email格式
if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false,'message' => 'Email格式不正確']);
    exit;
}


//檢查重複email

$checkSql ="SELECT COUNT(*) FROM users WHERE email = ?";
$checkStmt= $pdo->prepare($checkSql);
$checkStmt->execute([$email]);
$count = $checkStmt->fetchColumn();

if ($count > 0) {
    echo json_encode(['success' => false, 'message' => 'Email已存在']);
    exit;
}


try {
    $sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $age]);
    $id = $pdo->lastInsertId();

    echo json_encode([
        'success'=>true,
        'user'=>[
            'id'=>$id,
            'name'=>htmlspecialchars($name),
            'email'=>htmlspecialchars($email),
            'age'=>$age
        ]
    ]);

} catch (PDOException $e){
    echo json_encode([
        'success' => false,
        'message' => '資料庫錯誤: ' . $e->getMessage()
    ]);
}
?>