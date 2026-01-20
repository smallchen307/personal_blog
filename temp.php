<?php
// 1. 取得目前的網址路徑用於高亮判斷
$current_uri = $_SERVER['REQUEST_URI'];
?>

<nav class="container mx-auto flex justify-between items-center flex-wrap">
    <div class="flex items-center space-x-4">
        <a href="/index.php" class="text-2xl font-bold rounded-md hover:text-white transition duration-300">
            <span class="text-red-500">我的</span>視界
        </a>
        
        <div class="hidden md:flex space-x-6 ml-8 text-sm font-medium">
            <a href="/index.php" 
               class="transition duration-300 px-2 py-2 <?= ($current_uri == '/index.php' || $current_uri == '/') ? 'text-white border-b-2 border-red-500' : 'text-gray-400 hover:text-white' ?>">
               首頁
            </a>

            <a href="/category/photo" 
               class="transition duration-300 px-2 py-2 <?= strpos($current_uri, '/category/photo') !== false ? 'text-white border-b-2 border-red-500' : 'text-gray-400 hover:text-white' ?>">
               攝影視界
            </a>

            <a href="/category/blog" 
               class="transition duration-300 px-2 py-2 <?= strpos($current_uri, '/category/blog') !== false ? 'text-white border-b-2 border-red-500' : 'text-gray-400 hover:text-white' ?>">
               程式學習日誌
            </a>

            <a href="/aboutme.php" 
               class="transition duration-300 px-2 py-2 <?= $current_uri == '/aboutme.php' ? 'text-white border-b-2 border-red-500' : 'text-gray-400 hover:text-white' ?>">
               關於我
            </a>

            <a href="/shop.php" 
               class="transition duration-300 px-2 py-2 <?= $current_uri == '/shop.php' ? 'text-white border-b-2 border-red-500' : 'text-gray-400 hover:text-white' ?>">
               販賣部
            </a>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <?php if(isset($_SESSION['admin_id'])): ?>
            <div class="hidden lg:flex items-center space-x-4 border-l border-white/20 pl-4 ml-2">
                <span class="text-xs text-gray-400">Hi, <?= htmlspecialchars($_SESSION['admin_user'] ?? '管理者') ?></span>
                <a href="/user_manager.php" class="text-xs hover:text-white transition">用戶管理</a>
                <a href="/admin/post_manager.php" class="text-xs hover:text-white transition">文章管理</a>
            </div>
            <a href="/user_logout_api.php" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-full transition duration-300 shadow-md">
                登出
            </a>
        <?php else: ?>
            <a href="/user_login.php" class="hover:text-white transition duration-300 hidden md:block px-3 py-2 rounded-md text-sm">會員登入</a>
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