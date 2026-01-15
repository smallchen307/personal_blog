<?php
require 'db.php';

// 撈取已發布文章
$stmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmallChen's Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #e2e8f0; }
        .glass-card { 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.08); 
        }
        .glow:hover { box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
    </style>
</head>
<body class="selection:bg-blue-500/30">

    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-emerald-600/10 blur-[100px] rounded-full"></div>
    </div>

    <nav class="sticky top-0 z-50 px-6 py-4 border-b border-white/5 bg-slate-950/50 backdrop-blur-md">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold tracking-tighter hover:opacity-80 transition">SMALLCHEN<span class="text-blue-500">.</span></a>
            <div class="flex gap-6 items-center text-sm font-medium text-slate-400">
                <a href="admin/login.php" class="hover:text-white transition">登入後台</a>
                <a href="admin/post_manager.php" class="bg-blue-600/10 text-blue-400 px-4 py-2 rounded-full border border-blue-500/20 hover:bg-blue-600 hover:text-white transition">管理文章</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-16">
        <header class="mb-20 text-center">
            <h1 class="text-6xl font-bold mb-6 bg-gradient-to-b from-white to-slate-500 bg-clip-text text-transparent">探索。紀錄。成長</h1>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto">這是我存放程式學習、攝影作品與生活隨筆的數位空間。</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($posts)): ?>
                <div class="col-span-full text-center py-20 text-slate-500">
                    <p>目前還沒有發布的文章...</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <a href="post_view.php?slug=<?= $post['slug'] ?>" class="group">
                    <article class="glass-card rounded-[2rem] overflow-hidden glow transition-all duration-500 hover:-translate-y-2 h-full flex flex-col">
                        <div class="relative h-56 overflow-hidden">
                            <?php if ($post['cover_image']): ?>
                                <img src="<?= htmlspecialchars($post['cover_image']) ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-900 flex items-center justify-center text-5xl opacity-40">
                                    <?= $post['type'] === 'photo' ? '📸' : '✍️' ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 text-[10px] font-bold tracking-widest uppercase rounded-full bg-blue-500 text-white shadow-lg">
                                    <?= $post['type'] ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-grow">
                            <time class="text-xs text-slate-500 font-mono mb-3"><?= date('F j, Y', strtotime($post['created_at'])) ?></time>
                            <h2 class="text-2xl font-bold mb-4 group-hover:text-blue-400 transition"><?= htmlspecialchars($post['title']) ?></h2>
                            <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed mb-6">
                                <?= mb_strimwidth(strip_tags($post['content']), 0, 120, "...") ?>
                            </p>
                            <div class="mt-auto flex items-center text-blue-400 text-sm font-bold">
                                閱讀全文 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </article>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="text-center py-20 border-t border-white/5 text-slate-600 text-sm">
        &copy; <?= date('Y') ?> SmallChen Blog. Powered by PHP & Tailwind.
    </footer>

</body>
</html>