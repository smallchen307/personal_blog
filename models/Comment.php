<?php
// models/Comment.php

/**
 * =================================================================================
 * Comment 模型 (Comment Model)
 * =================================================================================
 * 
 * 這個類別（Class）專門用來處理與「評論 (comments)」資料表相關的所有資料庫操作。
 * 它封裝了資料庫的邏輯，讓控制器（Controller）的程式碼可以更簡潔、更專注於業務邏輯。
 * 這種將資料庫操作獨立出來的設計模式稱為「模型 (Model)」。
 * 
 */
class Comment {
    /**
     * @var PDO $db 資料庫連線物件。
     * private 關鍵字表示這個屬性只能在類別內部被存取，外部無法直接讀取或修改。
     */
    private $db;
    
    /**
     * 建構子 (Constructor)
     * 
     * 當我們使用 `new Comment($pdo)` 建立這個類別的實例（Instance）時，
     * 這個方法會被自動呼叫。
     * 它接收一個 PDO 資料庫連線物件，並將其儲存在私有屬性 `$this->db` 中，
     * 供這個類別的其他方法使用。
     * 
     * @param PDO $pdo 來自 db.php 的資料庫連線物件。
     */
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * 根據文章 ID 取得所有相關評論
     * 
     * 這個方法會從資料庫中選取特定文章（由 $post_id 指定）的所有評論，
     * 並按照創建時間（created_at）由舊到新（ASC）排序。
     * 
     * @param int $post_id 要查詢評論的文章 ID。
     * @return array 回傳一個包含所有評論資料的陣列。如果沒有評論，則回傳空陣列。
     */
    public function getCommentsByPostId($post_id) {
        // 1. 準備 SQL 查詢語句，使用 ? 作為預備處理的參數，可以防止 SQL 注入攻擊。
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
        
        // 2. 執行查詢，並將 $post_id 綁定到第一個 ? 的位置。
        $stmt->execute([$post_id]);
        
        // 3. 使用 fetchAll() 取得所有符合條件的紀錄，並以陣列形式回傳。
        return $stmt->fetchAll();
    }

    /**
     * 建立一筆新的評論
     * 
     * 這個方法負責將一筆新的評論資料插入到 `comments` 資料表中。
     * 
     * @param int    $post_id   評論所屬的文章 ID。
     * @param int    $parent_id 父評論的 ID。如果是主評論，此值為 0。
     * @param string $author    評論者的名稱。
     * @param string $content   評論的內容。
     * @return bool 回傳一個布林值。如果資料成功插入，則回傳 true；否則回傳 false。
     */
    public function createComments($post_id, $parent_id, $author, $content) {

        
        // 1. 準備 INSERT 的 SQL 語句。
        
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, parent_id, author_name, content) VALUES (?, ?, ?, ?)");
        // 修改點：將 0 轉換為 NULL 存入資料庫，以符合外鍵約束
        $db_parent_id = ($parent_id == 0) ? null : $parent_id;
        // 2. 執行 SQL 語句，並依序綁定所有參數。
        //    execute() 方法會回傳 true 或 false，代表執行是否成功。
        return $stmt->execute([$post_id, $db_parent_id, $author, $content]);
    }
}
