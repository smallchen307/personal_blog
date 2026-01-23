<?php
require 'db.php';
include 'header.php';
?>

<main class="container mx-auto py-16 px-4">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        <div class="md:w-1/3 bg-primary flex items-center justify-center p-12">
            <div class="text-center">
                <div class="w-40 h-40 bg-gray-300 rounded-full mx-auto mb-6 border-4 border-white shadow-lg overflow-hidden">
                    <img src="https://placehold.co/400x400/0d1a26/ffffff?text=ME" alt="Profile" class="w-full h-full object-cover">
                </div>
                <h2 class="text-2xl font-bold text-white">SmallChen</h2>
                <p class="text-primary-light text-sm opacity-70">Full-Stack Developer / Photographer</p>
            </div>
        </div>

        <div class="md:w-2/3 p-8 md:p-16">
            <h3 class="text-3xl font-bold text-gray-800 mb-6">關於我的視界</h3>
            <div class="prose prose-slate text-gray-600 leading-relaxed space-y-4">
                <p>
                    你好！我是 SmallChen。這裡是我記錄技術成長與生活光影的數位空間。
                    目前我正專注於從 PHP 開發邁向全端 AI 工程師的轉型之旅。
                </p>
                <p>
                    在「攝影視界」中，你可以看到我對光影的捕捉；在「程式學習日誌」中，
                    則記錄了我解決技術難題的點點滴滴。
                </p>
                <div class="pt-8 flex gap-4">
                    <a href="coding_log.php" class="bg-primary text-white px-6 py-2 rounded-full text-sm font-medium hover:opacity-90 transition">學習日誌</a>
                    <a href="photography.php" class="border border-primary text-primary px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-50 transition">查看作品</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>