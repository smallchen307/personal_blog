<laravel-boost-guidelines>
=== 基礎規則 ===

# Laravel Boost 指南

Laravel Boost 指南由 Laravel 維護人員專為此應用程式特別策劃。應嚴格遵守這些指南，以提高用戶在建構 Laravel 應用程式時的滿意度。

## 基礎背景
此應用程式是一個 Laravel 應用程式，其主要的 Laravel 生態系套件與版本如下。您是精通所有這些套件的專家。請確保您遵守這些特定的套件與版本。

- php - 8.4.14
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## 慣例
- 您必須遵循此應用程式中使用的所有現有程式碼慣例。在建立或編輯檔案時，請檢查同層級的檔案以了解正確的結構、方法和命名方式。
- 為變數和方法使用描述性的名稱。例如，`isRegisteredForDiscounts`，而不是 `discount()`。
- 在撰寫新元件之前，先檢查是否有現有的元件可以重複使用。

## 驗證腳本
- 當測試已經涵蓋了某項功能並證明其有效時，不要建立驗證腳本或使用 tinker。單元測試和功能測試更為重要。

## 應用程式結構與架構
- 堅持使用現有的目錄結構；未經批准，不要建立新的基礎資料夾。
- 未經批准，不要更改應用程式的依賴套件。

## 前端打包
- 如果使用者在 UI 中看不到前端的變更，這可能意味著他們需要執行 `npm run build`、`npm run dev` 或 `composer run dev`。請詢問他們。

## 回覆
- 您的解釋應簡潔扼要 - 專注於重要內容，而不是解釋顯而易見的細節。

## 說明文件
- 您必須僅在使用者明確要求時才建立說明文件。

=== boost 規則 ===

## Laravel Boost
- Laravel Boost 是一個 MCP 伺服器，附帶專為此應用程式設計的強大工具。請使用它們。

## Artisan
- 當您需要呼叫 Artisan 指令時，請使用 `list-artisan-commands` 工具來再次確認可用的參數。

## URLs
- 每當您與使用者分享專案 URL 時，都應使用 `get-absolute-url` 工具，以確保您使用的是正確的協定、網域/IP 和連接埠。

## Tinker / 除錯
- 當您需要執行 PHP 來除錯程式碼或直接查詢 Eloquent 模型時，應使用 `tinker` 工具。
- 當您只需要從資料庫讀取資料時，請使用 `database-query` 工具。

## 使用 `browser-logs` 工具讀取瀏覽器日誌
- 您可以使用 Boost 的 `browser-logs` 工具讀取瀏覽器日誌、錯誤和例外。
- 只有最近的瀏覽器日誌才有用 - 請忽略舊的日誌。

## 搜尋文件 (極為重要)
- 在處理 Laravel 或 Laravel 生態系套件時，Boost 附帶一個強大的 `search-docs` 工具，您應在嘗試任何其他方法之前優先使用它。此工具會自動將已安裝套件及其版本的列表傳遞給遠端的 Boost API，因此它只會返回針對使用者情況的特定版本文件。如果您知道需要特定套件的文件，您應該傳遞一個套件陣列進行過濾。
- `search-docs` 工具非常適用於所有與 Laravel 相關的套件，包括 Laravel、Inertia、Livewire、Filament、Tailwind、Pest、Nova、Nightwatch 等。
- 在考慮其他方法之前，您必須使用此工具來搜尋 Laravel 生態系的文件。
- 在進行程式碼變更之前，先搜尋文件，以確保我們採取了正確的方法。
- 開始時，請使用多個、廣泛、簡單、基於主題的查詢。例如：`['rate limiting', 'routing rate limiting', 'routing']`。
- 不要在查詢中加入套件名稱；套件資訊已經被分享了。例如，使用 `test resource table`，而不是 `filament 4 test resource table`。

### 可用的搜尋語法
- 您可以且應該一次傳遞多個查詢。最相關的結果將會最先返回。

1. 簡單單詞搜尋 (自動詞幹提取) - query=authentication - 可找到 'authenticate' 和 'auth'。
2. 多個單詞 (AND 邏輯) - query=rate limit - 找到同時包含 "rate" 和 "limit" 的知識。
3. 引號片語 (精確位置) - query="infinite scroll" - 單詞必須相鄰且順序相同。
4. 混合查詢 - query=middleware "rate limit" - "middleware" 且為精確片語 "rate limit"。
5. 多個查詢 - queries=["authentication", "middleware"] - 任何這些詞彙中的一個。

=== php 規則 ===

## PHP

- 即使只有一行，也應始終為控制結構使用大括號。

### 建構子
- 在 `__construct()` 中使用 PHP 8 的建構子屬性提升。
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- 除非建構子是私有的，否則不允許存在沒有參數的空 `__construct()` 方法。

### 型別宣告
- 始終為方法和函式使用明確的返回型別宣告。
- 為方法參數使用適當的 PHP 型別提示。

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## 註解
- 偏好使用 PHPDoc 區塊而非行內註解。除非有非常複雜的情況，否則絕不在程式碼內部使用註解。

## PHPDoc 區塊
- 在適當的時候，為陣列添加有用的陣列形狀 (array shape) 型別定義。

## 列舉 (Enums)
- 通常，Enum 中的鍵應使用標題格 (TitleCase)。例如：`FavoritePerson`、`BestLake`、`Monthly`。

=== laravel/core 規則 ===

## 遵循 Laravel 的風格

- 使用 `php artisan make:` 指令來建立新檔案 (例如：migrations、controllers、models 等)。您可以使用 `list-artisan-commands` 工具列出可用的 Artisan 指令。
- 如果您要建立一個通用的 PHP 類別，請使用 `php artisan make:class`。
- 將 `--no-interaction` 傳遞給所有 Artisan 指令，以確保它們在沒有使用者輸入的情況下也能運作。您也應該傳遞正確的 `--options` 以確保行為正確。

### 資料庫
- 始終使用帶有返回型別提示的 Eloquent 關聯方法。優先使用關聯方法，而不是原始查詢或手動 join。
- 在建議原始資料庫查詢之前，先使用 Eloquent 模型和關聯。
- 避免使用 `DB::`；優先使用 `Model::query()`。產生的程式碼應利用 Laravel 的 ORM 功能，而不是繞過它們。
- 透過使用預先載入 (eager loading) 來產生防止 N+1 查詢問題的程式碼。
- 對於非常複雜的資料庫操作，使用 Laravel 的查詢建構器。

### 模型建立
- 在建立新模型時，也為它們建立有用的工廠 (factories) 和填充器 (seeders)。詢問使用者是否需要任何其他東西，並使用 `list-artisan-commands` 來檢查 `php artisan make:model` 的可用選項。

### API 與 Eloquent 資源
- 對於 API，預設使用 Eloquent API 資源和 API 版本控制，除非現有的 API 路由不這樣做，那麼您應該遵循現有的應用程式慣例。

### 控制器與驗證
- 始終為驗證建立 Form Request 類別，而不是在控制器中使用行內驗證。應包含驗證規則和自訂錯誤訊息。
- 檢查同層級的 Form Request，看看應用程式是使用基於陣列還是字串的驗證規則。

### 隊列 (Queues)
- 對於耗時的操作，使用帶有 `ShouldQueue` 介面的隊列任務 (queued jobs)。

### 認證與授權
- 使用 Laravel 內建的認證和授權功能 (gates, policies, Sanctum 等)。

### URL 生成
- 在產生指向其他頁面的連結時，優先使用命名路由和 `route()` 函式。

### 設定
- 僅在設定檔中使用環境變數 - 絕不在設定檔之外直接使用 `env()` 函式。始終使用 `config('app.name')`，而不是 `env('APP_NAME')`。

### 測試
- 在為測試建立模型時，請使用該模型的工廠。在手動設定模型之前，檢查工廠是否有可以使用的自訂狀態。
- Faker: 使用如 `$this->faker->word()` 或 `fake()->randomDigit()` 的方法。遵循現有慣例，決定是使用 `$this->faker` 還是 `fake()`。
- 在建立測試時，使用 `php artisan make:test [options] {name}` 來建立功能測試，並傳遞 `--unit` 來建立單元測試。大多數測試應該是功能測試。

### Vite 錯誤
- 如果您收到 "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" 錯誤，您可以執行 `npm run build` 或要求使用者執行 `npm run dev` 或 `composer run dev`。

=== laravel/v12 規則 ===

## Laravel 12

- 使用 `search-docs` 工具獲取特定版本的文件。
- 自 Laravel 11 起，Laravel 採用了新的簡化檔案結構，本專案也使用此結構。

### Laravel 12 結構
- 在 Laravel 12 中，中介層 (middleware) 不再於 `app/Http/Kernel.php` 中註冊。
- 中介層在 `bootstrap/app.php` 中使用 `Application::configure()->withMiddleware()` 以宣告方式進行設定。
- `bootstrap/app.php` 是註冊中介層、例外處理和路由檔案的地方。
- `bootstrap/providers.php` 包含應用程式特定的服務提供者。
- `app\Console\Kernel.php` 檔案已不復存在；請使用 `bootstrap/app.php` 或 `routes/console.php` 進行主控台設定。
- `app/Console/Commands/` 中的主控台指令會自動可用，無需手動註冊。

### 資料庫
- 修改欄位時，遷移 (migration) 必須包含該欄位上先前定義的所有屬性。否則，它們將被丟棄並遺失。
- Laravel 12 允許原生地限制預先載入的記錄數量，無需外部套件：`$query->latest()->limit(10);`。

### 模型
- 型別轉換 (Casts) 可以且可能應該在模型的 `casts()` 方法中設定，而不是在 `$casts` 屬性中。請遵循其他模型的現有慣例。

=== pint/core 規則 ===

## Laravel Pint 程式碼格式化工具

- 在最終確定變更之前，您必須執行 `vendor/bin/pint --dirty`，以確保您的程式碼符合專案預期的風格。
- 不要執行 `vendor/bin/pint --test`，只需執行 `vendor/bin/pint` 來修復任何格式問題。

=== phpunit/core 規則 ===

## PHPUnit

- 此應用程式使用 PHPUnit 進行測試。所有測試都必須撰寫為 PHPUnit 類別。使用 `php artisan make:test --phpunit {name}` 來建立新測試。
- 如果您看到使用 "Pest" 的測試，請將其轉換為 PHPUnit。
- 每當測試被更新後，就單獨執行該測試。
- 當與您功能相關的測試都通過時，詢問使用者是否也想執行整個測試套件，以確保一切仍然通過。
- 測試應該測試所有的成功路徑、失敗路徑和異常路徑。
- 未經批准，您不得從 tests 目錄中刪除任何測試或測試檔案。這些不是暫存或輔助檔案；它們是應用程式的核心。

### 執行測試
- 在最終確定前，使用適當的過濾器執行最少數量的測試。
- 執行所有測試：`php artisan test --compact`。
- 執行檔案中的所有測試：`php artisan test --compact tests/Feature/ExampleTest.php`。
- 過濾特定測試名稱：`php artisan test --compact --filter=testName` (建議在對相關檔案進行變更後使用)。

=== tailwindcss/core 規則 ===

## Tailwind CSS

- 使用 Tailwind CSS 類別來為 HTML 設定樣式；在撰寫自己的樣式之前，請檢查並使用專案中現有的 Tailwind 慣例。
- 提議將重複的模式提取到符合專案慣例 (例如 Blade, JSX, Vue 等) 的元件中。
- 仔細考慮類別的放置、順序、優先級和預設值。刪除多餘的類別，謹慎地將類別添加到父或子元素以限制重複，並按邏輯分組元素。
- 需要時，您可以使用 `search-docs` 工具從官方文件中獲取確切的範例。

### 間距
- 列出項目時，使用 gap 工具程式來設定間距；不要使用 margin。

<code-snippet name="Valid Flex Gap Spacing Example" lang="html">
    <div class="flex gap-8">
        <div>Superior</div>
        <div>Michigan</div>
        <div>Erie</div>
    </div>
</code-snippet>

### 深色模式
- 如果現有頁面和元件支援深色模式，則新頁面和元件也必須以類似的方式支援深色模式，通常使用 `dark:`。

=== tailwindcss/v4 規則 ===

## Tailwind CSS 4

- 始終使用 Tailwind CSS v4；不要使用已棄用的工具類。
- Tailwind v4 不支援 `corePlugins`。
- 在 Tailwind v4 中，設定是 CSS 優先的，使用 `@theme` 指令 — 不再需要單獨的 `tailwind.config.js` 檔案。

<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- 在 Tailwind v4 中，您使用常規的 CSS `@import` 語句來匯入 Tailwind，而不是使用 v3 中的 `@tailwind` 指令：

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>

### 被取代的工具類
- Tailwind v4 移除了已棄用的工具類。不要使用棄用的選項；請使用替代選項。
- 透明度值仍然是數字。

| 已棄用 | 取代方案 |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
