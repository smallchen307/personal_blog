<?php
// 引入資料庫設定檔，建立與資料庫的連接
require 'db.php';
// 引入統一的頁首（Header）檔案，通常包含 HTML 的 <head>、導覽列等
require 'header.php'; 

/**
 * =================================================================================
 * 文章資料撈取與分類
 * =================================================================================
 * 這裡的程式碼主要負責從資料庫中撈取所有「已發布」的文章，
 * 然後根據文章的類型（'photo' 或 其他）將它們分類到不同的陣列中，
 * 以便在後續的 HTML 中分別顯示。
 */

// 準備並執行一個 SQL 查詢，從 'posts' 資料表中選取所有狀態（status）為 'published' 的文章
// 並按照創建時間（created_at）降序（DESC）排列，最新的文章會排在最前面
$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");

// 從查詢結果中獲取所有文章，並存儲在 $all_posts 變數中
// fetchAll() 會回傳一個包含所有結果列的陣列
$all_posts = $stmt->fetchAll();

// 使用 array_filter 函數過濾出類型為 'photo' 的文章
// 這個匿名函數會檢查每篇文章的 'type' 欄位，如果等於 'photo'，則保留該文章
$photo_posts = array_filter($all_posts, function($p) { return $p['type'] === 'photo'; });

// 同樣使用 array_filter，但這次是過濾出類型「不是」'photo' 的文章
// 這通常是部落格文章或學習日誌等
$daily_posts = array_filter($all_posts, function($p) { return $p['type'] !== 'photo'; });

// 設定一個固定的 Hero Section (頁首大圖) 的背景圖片路徑
// 這樣可以方便地在一個地方管理這個圖片，而不是寫死在 HTML 中
$fixed_hero_img = '/uploads/firework.jpg';
?>

    <!-- 
    =================================================================================
    Hero Section (頁首大圖區塊)
    =================================================================================
    這是一個視覺上很引人注目的區塊，通常位於頁面最上方，用來吸引用戶的注意力。
    背景圖片是動態載入的，上面覆蓋了一個半透明的黑色遮罩，以增強文字的可讀性。
    -->
    <section class="relative bg-cover bg-center h-[500px] md:h-[600px] flex items-center justify-center text-white"
             style="background-image: url('<?= $fixed_hero_img ?>');">
        <!-- 半透明黑色遮罩 -->
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <!-- Hero Section 中間的文字內容 -->
        <div class="relative text-center p-6 max-w-2xl mx-auto rounded-lg">
            <h1 class="text-4xl md:text-[3.5rem] font-bold mb-4 leading-tight">
                紀錄我的創意與學習之旅
            </h1>
            <p class="text-lg md:text-xl mb-8">這裡融合了我的攝影作品、學習歷程與知識分享。</p>
            <!-- 兩個主要的行動呼籲 (Call-to-Action) 按鈕 -->
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#photo-section" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300">
                    進入攝影視界
                </a>
                <a href="#daily-section" class="bg-white text-gray-900 hover:bg-gray-100 font-semibold py-3 px-8 rounded-full shadow-lg transition duration-300">
                    查看學習日誌
                </a>
            </div>
        </div>
    </section>

    <!-- 
    =================================================================================
    主內容區塊 (Main Content)
    =================================================================================
    這個區塊包含了頁面的主要內容，分為「精選攝影」和「最新學習日誌」兩個部分。
    -->
    <main class="container mx-auto py-12 px-4">
        <!-- 
        精選攝影區塊 (Photo Section)
        使用 ID "photo-section" 以便 Hero Section 的按鈕可以連結到這裡。
        -->
        <section id="photo-section" class="mb-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">精選攝影內容</h2>
            <!-- 
            使用 CSS Grid 佈局來呈現卡片列表。
            - grid-cols-1: 在最小的螢幕上，每行一個卡片。
            - sm:grid-cols-2: 在小螢幕及以上，每行兩個。
            - lg:grid-cols-3: 在大螢幕及以上，每行三個。
            - xl:grid-cols-4: 在超大螢幕及以上，每行四個。
            -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php 
                // 使用 foreach 迴圈遍歷攝影文章陣列
                // array_slice($photo_posts, 0, 4) 只選取最新的 4 篇文章來顯示
                foreach (array_slice($photo_posts, 0, 4) as $post): 
                ?>
                <div class="card-bg rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition duration-300 border border-gray-100 bg-white">
                    <!-- 
                    文章封面圖片
                    - htmlspecialchars() 用來防止 XSS 攻擊。
                    - 三元運算子 (?:) 檢查是否有封面圖片，若無，則顯示一個預設的佔位圖片。
                    -->
                    <img src="<?= htmlspecialchars($post['cover_image']) ?: 'https://placehold.co/400x225?text=No+Image' ?>" alt="攝影" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($post['title']) ?></h3>
                        <!-- 
                        文章內容預覽
                        - strip_tags() 移除 HTML 標籤，只顯示純文字。
                        - mb_strimwidth() 用於截斷字串，確保預覽內容不會太長，並在結尾加上 "..."。
                        -->
                        <p class="text-gray-600 text-sm line-clamp-2"><?= mb_strimwidth(strip_tags($post['content']), 0, 60, "...") ?></p>
                        <!-- 連結到完整的文章頁面，使用文章的 'slug' 作為唯一識別碼 -->
                        <a href="post_view.php?slug=<?= $post['slug'] ?>" class="inline-block mt-4 text-red-500 hover:text-red-600 font-medium transition duration-300">觀看文章 &rarr;</a>
                    </div>
                </div>
                <?php endforeach; // 結束迴圈 ?>
            </div>
        </section>

        <!-- 
        最新學習日誌區塊 (Daily Section)
        使用 ID "daily-section" 以便 Hero Section 的按鈕可以連結到這裡。
        -->
        <section id="daily-section" class="mb-16 bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">最新學習日誌</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php 
                // 同樣地，只選取最新的 4 篇日誌文章來顯示
                foreach (array_slice($daily_posts, 0, 4) as $post): 
                ?>
                <div class="card-bg rounded-lg shadow-sm overflow-hidden border border-gray-200">
                    <div class="p-5">
                        <!-- 顯示文章類型，例如 'coding', 'life' 等 -->
                        <span class="text-sm font-semibold text-blue-600 mb-2 block uppercase"><?= htmlspecialchars($post['type']) ?></span>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= mb_strimwidth(strip_tags($post['content']), 0, 100, "...") ?></p>
                        <div class="flex justify-between items-center">
                            <!-- 
                            顯示文章的創建日期
                            - strtotime() 將資料庫中的日期時間字串轉換為 Unix 時間戳。
                            - date() 將時間戳格式化為 '年/月/日' 的格式。
                            -->
                            <span class="text-gray-500 text-xs"><?= date('Y/m/d', strtotime($post['created_at'])) ?></span>
                            <!-- 連結到完整的文章頁面 -->
                            <a href="post_view.php?slug=<?= $post['slug'] ?>" class="text-blue-500 hover:text-blue-600 font-medium transition duration-300">閱讀更多 &rarr;</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; // 結束迴圈 ?>
            </div>
        </section>
    </main>

<?php 
// 引入統一的頁腳（Footer）檔案，通常包含版權資訊、聯絡方式等
include 'footer.php'; 
?>
