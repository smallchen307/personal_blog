<?php
// 確保 Session 已啟動
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的視界 - 個人綜合平台</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8f8f8; 
        }
        /* 1:1 復刻模板定義的自定義顏色 */
        .bg-primary { background-color: #0d1a26; } 
        .text-primary-light { color: #e0e7ff; } 
        .bg-secondary { background-color: #1a2b3c; } 
        .btn-accent { background-color: #ef4444; } 
        .btn-accent:hover { background-color: #dc2626; }
        
        /* 為了讓管理後台頁面也有基礎一致感，定義 admin 專用卡片樣式 */
        .admin-card { 
            background: white; 
            border-radius: 0.5rem; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
            padding: 1.5rem; 
        }

        /* 完善後的 Markdown 內容樣式 */
        .markdown-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #374151;
        }
        .markdown-content h1, .markdown-content h2, .markdown-content h3 {
            color: #111827;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
        }
        .markdown-content h1 { font-size: 2.25rem; }
        .markdown-content h2 { font-size: 1.85rem; border-bottom: 2px solid #f3f4f6; pb-2; }
        .markdown-content p { margin-bottom: 1.5rem; }
        .markdown-content a { color: #3b82f6; text-decoration: underline; }
        .markdown-content ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .markdown-content img { max-width: 100%; border-radius: 0.5rem; margin: 2rem 0; }
        .markdown-content code:not(pre code) { 
            background: #fee2e2; 
            color: #991b1b; 
            padding: 0.2rem 0.4rem; 
            border-radius: 0.25rem; 
            font-size: 0.9em; 
}
    </style>
</head>
<body class="antialiased">

    <header class="bg-primary text-primary-light p-4 shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center flex-wrap">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="text-2xl font-bold rounded-md hover:text-white transition duration-300">
                    <span class="text-red-500">我的</span>視界
                </a>
                
                <div class="hidden md:flex space-x-6 ml-8 text-sm font-medium">
                    <a href="index.php" class="hover:text-white transition duration-300 px-2 py-2">首頁</a>
                    <a href="photography.php" class="hover:text-white transition duration-300 px-2 py-2">攝影視界</a>
                    <a href="coding_log.php" class="hover:text-white transition duration-300 px-2 py-2">程式學習日誌</a>
                    <a href="aboutme.php" class="hover:text-white transition duration-300 px-2 py-2">關於我</a>
                    <a href="shop.php" class="hover:text-white transition duration-300 px-2 py-2">販賣部</a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION['admin_id'])): ?>
                    <div class="hidden lg:flex items-center space-x-4 border-l border-white/20 pl-4 ml-2">
                        <span class="text-xs text-gray-400">Hi, <?= htmlspecialchars($_SESSION['admin_user'] ?? '管理者') ?></span>
                        <a href="user_manager.php" class="text-xs hover:text-white transition">用戶管理</a>
                        <a href="admin/post_manager.php" class="text-xs hover:text-white transition">文章管理</a>
                    </div>
                    <a href="user_logout_api.php" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-full transition duration-300 shadow-md">
                        登出
                    </a>
                <?php else: ?>
                    <a href="user_login.php" class="hover:text-white transition duration-300 hidden md:block px-3 py-2 rounded-md text-sm">會員登入</a>
                    <a href="#" class="hover:text-white transition duration-300 px-3 py-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                        </svg>
                    </a>
                <?php endif; ?>

                <button class="md:hidden text-primary-light focus:outline-none ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </nav>
    </header>