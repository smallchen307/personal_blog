<?php
require 'db.php';
include 'header.php'; // 直接套用你最滿意的 Header
?>

<main class="container mx-auto py-12 px-4">
    <div class="flex justify-between items-end mb-8 border-b border-gray-200 pb-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">攝影視界</h2>
            <p class="text-gray-500 mt-2">透過鏡頭記錄生活的瞬間與光影</p>
        </div>
        <span class="text-sm font-mono text-gray-400">Total: <?php echo $pdo->query("SELECT count(*) FROM posts WHERE type='photo'")->fetchColumn(); ?> Works</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $stmt = $pdo->query("SELECT * FROM posts WHERE type='photo' AND status='published' ORDER BY created_at DESC");
        $photos = $stmt->fetchAll();
        
        foreach ($photos as $p): ?>
            <a href="post_view.php?slug=<?= $p['slug'] ?>" class="group">
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="aspect-square overflow-hidden">
                        <img src="<?= htmlspecialchars($p['cover_image']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 group-hover:text-red-500 transition"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="text-xs text-gray-400 mt-1"><?= date('Y-m-d', strtotime($p['created_at'])) ?></p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'footer.php'; ?>