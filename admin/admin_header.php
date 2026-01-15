<?php
// admin_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>管理後台 - 綜合平台</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            /* 為了對齊 Gemini 樣板，稍微自定義背景色 */
            .bg-primary { background-color: #294e70; } /*#0d1a26*/
            .bg-secondary { background-color: #3c5f82; }/*#1a2b3c*/
        </style>
    </head>
    <body class="bg-gray-900 text-gray-100 min-h-screen">

        <nav class="bg-primary border-b border-gray-700 px-6 py-4 mb-10 shadow-lg">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex gap-8 items-center">
                    <span class="text-xl font-bold text-blue-400 tracking-wider">控制台</span>
                    <div class="hidden md:flex gap-6">
                        <a href="../index.php" class="text-gray-300 hover:text-white transition">會員管理</a>
                        <a href="post_manager.php" class="text-gray-300 hover:text-white transition">內容管理</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-400 italic">
                        <?= htmlspecialchars($_SESSION['admin_user'] ?? '管理者') ?>
                    </span>
                    <a href="../logout.php" class="bg-red-500/80 hover:bg-red-600 text-white text-sm px-4 py-1.5 rounded-full transition">
                        登出
                    </a>
                </div>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-6 pb-20">