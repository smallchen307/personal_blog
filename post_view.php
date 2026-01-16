<?php
require 'db.php';

// 1. 取得網址上的 slug
$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: index.php');
    exit;
}

// 2. 撈取該篇文章資料
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

// 3. 如果找不到文章，退回首頁
if (!$post) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - SmallChen Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #020617; color: #e2e8f0; line-height: 1.8; }
        .glass-panel { 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
        }
        /* 針對文章內文的樣式優化 */
        .prose img { border-radius: 1rem; margin: 2rem 0; border: 1px solid rgba(255,255,255,0.1); }
        .prose p { margin-bottom: 1.5rem; color: #cbd5e1; font-size: 1.125rem; }
    </style>
</head>
<body class="selection:bg-blue-500/30">

    <nav class="px-6 py-4 border-b border-white/5 bg-slate-950/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold tracking-tighter flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                BACK
            </a>
            <span class="text-xs font-mono text-slate-500 uppercase tracking-widest"><?= $post['type'] ?></span>
        </div>
    </nav>

    <article class="max-w-4xl mx-auto px-6 py-12">
        <header class="mb-12 text-center">
            <time class="text-blue-400 font-mono text-sm mb-4 block"><?= date('F j, Y', strtotime($post['created_at'])) ?></time>
            <h1 class="text-4xl md:text-6xl font-extrabold mb-8 bg-gradient-to-b from-white to-slate-400 bg-clip-text text-transparent leading-tight">
                <?= htmlspecialchars($post['title']) ?>
            </h1>
            
            <?php if ($post['cover_image']): ?>
                <div class="rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/10 shadow-blue-500/5">
                    <img src="<?= htmlspecialchars($post['cover_image']) ?>" alt="Cover" class="w-full object-cover max-h-[500px]">
                </div>
            <?php endif; ?>
        </header>

        <div class="glass-panel rounded-[2.5rem] p-8 md:p-16 mb-20">
            <div class="prose prose-invert max-w-none">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </div>

        <footer class="border-t border-white/5 pt-10 text-center">
            <p class="text-slate-500 text-sm mb-6">感謝你的閱讀</p>
            <a href="index.php" class="inline-block bg-white text-slate-950 px-8 py-3 rounded-full font-bold hover:bg-blue-500 hover:text-white transition shadow-xl">
                返回文章列表
            </a>
        </footer>
    </article>

</body>
</html>