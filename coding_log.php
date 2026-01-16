<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>程式學習日誌 - 我的視界</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f8f8; }
        .bg-primary { background-color: #0d1a26; }
    </style>
</head>
<body>
    <main class="container mx-auto py-12 px-4 max-w-5xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">程式學習日誌 <span class="text-sm font-normal text-gray-500">Dev Log</span></h2>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                <h3 class="text-xl font-bold">預覽：PHP 與 Tailwind 的整合開發</h3>
                <p class="text-gray-600 mt-2">這裡是日誌內容的摘要預覽...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                <h3 class="text-xl font-bold">預覽：從 PHP 轉向 Next.js 的思考</h3>
                <p class="text-gray-600 mt-2">未來搬遷到 React 框架的架構規劃...</p>
            </div>
        </div>
    </main>
</body>
</html>