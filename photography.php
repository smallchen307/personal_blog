<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>攝影視界 - 我的視界</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f8f8; }
        .bg-primary { background-color: #0d1a26; }
    </style>
</head>
<body>
    <?php include 'header_template.php'; // 建議將 Header 獨立出來，或是直接貼上原本的 Header ?>
    
    <main class="container mx-auto py-12 px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">攝影視界 <span class="text-sm font-normal text-gray-500">Photography Gallery</span></h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-200 aspect-square rounded-lg flex items-center justify-center text-gray-400">攝影作品預覽區</div>
            <div class="bg-gray-200 aspect-square rounded-lg flex items-center justify-center text-gray-400">攝影作品預覽區</div>
            <div class="bg-gray-200 aspect-square rounded-lg flex items-center justify-center text-gray-400">攝影作品預覽區</div>
            <div class="bg-gray-200 aspect-square rounded-lg flex items-center justify-center text-gray-400">攝影作品預覽區</div>
        </div>
    </main>
</body>
</html>