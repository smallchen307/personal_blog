# Laravel Blog 開發檢查表與學習指南

## 概述
本專案從簡單 PHP blog（old_blog）轉移到 Laravel（my-project），補全功能，建立完整 blog 系統。目標：學習 Laravel，實現現代化 blog。使用此檢查表逐步完成，並參考詳細指引。

## 架構設計
- **前端**：Inertia.js + React（動態互動，取代舊 HTML）。
- **後端**：MVC 架構。
  - Model：Eloquent 模型（Post, Comment, User）。
  - View：React 元件（Posts/Index.jsx）。
  - Controller：處理邏輯（PostController）。
- **路由**：RESTful（/posts, /posts/{id}）。
- **資料庫**：MySQL，遷移管理。
- **認證**：Breeze（登入/註冊）。

## 階段 1: 基礎架構與分析
- [ ] 分析舊程式碼結構（old_blog），映射功能到 Laravel。
  - **指引**：讀取 old_blog/index.php, category.php, post_view.php。記錄邏輯（如查詢 posts）。
  - **學習**：認識 MVC – 舊的是混合，Laravel 分離。
- [ ] 設計整體架構（MVC + Inertia/React）。
  - **指引**：繪製圖：用戶請求 → Route → Controller → Model → DB → Inertia → React。
- [ ] 建立 Laravel 專案基礎（Model + 遷移）。
  - **指引**：php artisan make:model Post -m，建立 posts 表（id, title, content, status, created_at）。
  - **程式碼**：migration: $table->string('title'); $table->text('content');

## 階段 2: 核心功能遷移
- [ ] 建立 PostController 和路由。
  - **指引**：php artisan make:controller PostController，建立 index() 方法。
  - **程式碼**：Route::get('/', [PostController::class, 'index']);
- [ ] 遷移首頁文章列表。
  - **指引**：PostController@index: $posts = Post::all(); return Inertia::render('Posts/Index', compact('posts'));
  - **學習**：Eloquent 取代手寫 SQL。
- [ ] 遷移分類頁面。
  - **指引**：Route::get('/category/{type}', [PostController::class, 'category']); 查詢 where('type', $type)。
- [ ] 遷移文章詳情頁面。
  - **指引**：Route::get('/posts/{post}', [PostController::class, 'show']); 載入關聯 comments。
- [ ] 調整留言系統（匿名留言 + 會員權限 + @ 標注）。
  - **指引**：Comment 模型允許 user_id null。Controller 檢查權限。支援 @username 高亮。

## 階段 3: 高優先補全功能
- [ ] 新增分頁功能。
  - **指引**：$posts = Post::paginate(10); 前端顯示分頁連結。
- [ ] 新增搜尋功能。
  - **指引**：if ($request->search) { $query->where('title', 'like', '%'.$request->search.'%'); }
- [ ] 新增 RSS Feed。
  - **指引**：Route::get('/feed', fn() => response()->view('feed', ['posts' => Post::latest()->limit(20)->get()])->header('Content-Type', 'application/rss+xml');
- [ ] 新增社群分享按鈕。
  - **指引**：前端 JS：window.open('https://twitter.com/share?url=' + url);

## 階段 4: 中優先補全功能
- [ ] 實作標籤系統。
  - **指引**：Tag 模型，posts_tags 表。多對多關聯。
- [ ] 實作分類樹。
  - **指引**：Category 模型，parent_id，遞歸查詢。
- [ ] 實作歸檔頁面。
  - **指引**：Route::get('/archive/{year}', fn($year) => Post::whereYear('created_at', $year)->get());
- [ ] 實作喜歡/評分系統。
  - **指引**：Like 模型，AJAX 請求。

## 階段 5: 後台與會員管理
- [ ] 補全文章編輯欄位（標籤、關鍵字、摘要等）。
  - **指引**：Post 遷移新增欄位，Controller 處理，表單新增 input。
- [ ] 實作會員角色與權限。
  - **指引**：composer require spatie/laravel-permission; $user->assignRole('admin');
- [ ] 實作個人資料管理。
  - **指引**：UserController@update，處理 name, email 等。
- [ ] 實作密碼重置與 Email 驗證。
  - **指引**：Breeze 已支援，啟用 middleware。

## 階段 6: 低優先與優化
- [ ] 新增 SEO 優化。
  - **指引**：Meta 標籤，Sitemap 生成。
- [ ] 新增聯絡表單。
  - **指引**：ContactController，Mail 發送。
- [ ] 新增多媒體支援。
  - **指引**：檔案上傳到 storage。
- [ ] 新增快取與統計。
  - **指引**：Cache::remember(), Google Analytics。

## 測試與部署
- [ ] 為每個功能寫 PHPUnit 測試。
  - **指引**：php artisan make:test PostTest; $this->assertCount(1, Post::all());
- [ ] 整合所有功能，端到端測試。
- [ ] 部署到生產環境。
  - **指引**：使用 Heroku 或 VPS，設定 .env。

## 注意事項
- 逐步實作，每天 1-2 小時。
- 參考 Laravel 文件：https://laravel.com/docs。
- 更新此檔，記錄進度。
- 與我互動：說 "開始 [項目]"，我協助。

更新日期：2026-01-23
進度：0%（開始前）
版本：整合學習版