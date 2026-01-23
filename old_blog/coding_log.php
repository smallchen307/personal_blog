<?php
require 'db.php';
include 'header.php';
?>

<main class="container mx-auto py-12 px-4 max-w-5xl">
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800">程式學習日誌</h2>
        <p class="text-gray-500 mt-2">記錄從 PHP 邁向全端 AI 工程師的開發心得</p>
    </div>

    <div class="space-y-8">
        <?php
        $stmt = $pdo->query("SELECT * FROM posts WHERE type!='photo' AND status='published' ORDER BY created_at DESC");
        while($log = $stmt->fetch()): ?>
            <article class="bg-white p-8 rounded-lg shadow-sm border-l-4 border-blue-500 hover:shadow-md transition">
                <div class="flex items-center gap-4 mb-3 text-sm text-blue-600 font-semibold uppercase">
                    <span><?= htmlspecialchars($log['type']) ?></span>
                    <span class="text-gray-300">|</span>
                    <span class="text-gray-400 font-normal"><?= date('M d, Y', strtotime($log['created_at'])) ?></span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4 hover:text-blue-600 transition">
                    <a href="post_view.php?slug=<?= $log['slug'] ?>"><?= htmlspecialchars($log['title']) ?></a>
                </h3>
                <p class="text-gray-600 line-clamp-3 leading-relaxed">
                    <?= mb_strimwidth(strip_tags($log['content']), 0, 200, "...") ?>
                </p>
                <a href="post_view.php?slug=<?= $log['slug'] ?>" class="inline-block mt-6 text-blue-500 font-medium hover:underline">閱讀全文 &rarr;</a>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php include 'footer.php'; ?>