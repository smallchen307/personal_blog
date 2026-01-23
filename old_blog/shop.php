<?php
require 'db.php';
include 'header.php';
?>

<main class="container mx-auto py-12 px-4">
    <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">販賣部</h2>
        <p class="text-gray-500">精選器材與數位資源分享</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition duration-300">
            <img src="https://placehold.co/400x300/FF5722/FFFFFF?text=專業相機" alt="專業相機" class="w-full h-48 object-cover">
            <div class="p-5">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">專業級單眼相機 XYZ-Pro</h3>
                <p class="text-gray-600 text-sm mb-3">高畫質感光元件，極速對焦，專為創作者打造。</p>
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-red-600">NT$ 65,000</span>
                    <button class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md font-medium shadow-md transition duration-300">
                        加入購物車
                    </button>
                </div>
            </div>
        </div>

        <div class="border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center text-gray-400 min-h-[350px]">
            商品上架中...
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>