import { Head, Link } from '@inertiajs/react';

// 注意：post 是從 Controller 的 Inertia::render 傳進來的
export default function Show({ post }) {
    return (
        <div className="min-h-screen bg-black text-gray-100 p-6 md:p-20">
            <Head title={post.title} />

            <div className="max-w-3xl mx-auto">
                {/* 導航區：返回按鈕 */}
                <Link 
                    href="/" 
                    className="text-blue-400 hover:text-blue-300 transition-colors mb-8 inline-block"
                >
                    ← 返回文章列表
                </Link>

                <article className="space-y-8">
                    {/* 標題區 */}
                    <header className="border-b border-gray-800 pb-8">
                        <h1 className="text-4xl md:text-5xl font-extrabold text-white leading-tight">
                            {post.title}
                        </h1>
                        <div className="mt-4 text-gray-500 text-sm flex items-center gap-4">
                            <span>文章 ID: {post.id}</span>
                            {/* 如果你有發布日期欄位，可以放在這 */}
                        </div>
                    </header>

                    {/* 內文區 */}
                    {/* 使用 dangerouslySetInnerHTML 來解析你舊資料庫裡的 HTML 標籤 */}
                    <div 
                        className="prose prose-invert prose-blue max-w-none leading-relaxed text-lg text-gray-300"
                        dangerouslySetInnerHTML={{ __html: post.content }}
                    />
                </article>

                {/* 底部導航 */}
                <div className="mt-20 pt-8 border-t border-gray-800 text-center">
                    <Link 
                        href="/" 
                        className="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all shadow-lg"
                    >
                        回到首頁
                    </Link>
                </div>
            </div>
        </div>
    );
}