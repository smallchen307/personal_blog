<?php
require 'auth_check.php';
require 'db.php';
header('Content-Type: application/json');
$id    = $_POST['id'] ?? '';
$name  = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$age   = $_POST['age'] ?? '';

if ($id === ''||$name ===''||$age ==='') {
    echo json_encode(['success' => false, 'message' => '資料不完整']);
    exit;
}

try {
    $sql = "UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name, $email, $age, $id]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $id,
                'name' => htmlspecialchars($name),
                'email' => htmlspecialchars($email),
                'age' => $age
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => '更新失敗']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit; // 確保後面沒有多餘的輸出
?>
