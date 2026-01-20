# AGENTS.md - Development Guidelines for Agentic Coding Agents

This file contains build commands, testing procedures, and code style guidelines for agentic coding agents working in this repository.

## Project Overview

This is a dual-project workspace containing:
1. **Main PHP Project** (root) - Traditional PHP blog/CMS with vanilla PHP, MySQL, and Tailwind CSS
2. **Laravel Project** (my-project/) - Modern Laravel application with React and Inertia.js

## Main PHP Project Commands & Testing

### Build Commands
```bash
# No formal build system - traditional PHP structure
# Development server requires Apache with mod_rewrite enabled

# Database connection test
php test_htaccess.php

# Check PHP syntax
php -l filename.php
```

### Testing Commands
```bash
# No formal testing framework - manual testing only
# Test database connection:
php -f db.php

# Test URL rewriting:
# Access URLs directly in browser to test .htaccess rules
# /category/photo -> category.php?type=photo
# /post/123 -> post_view.php?id=123
```

### Linting/Formatting
```bash
# No automated linting tools configured
# Manual code review required for:
# - PHP syntax errors
# - SQL injection vulnerabilities
# - XSS prevention
```

## Laravel Project Commands (my-project/)

### Build Commands
```bash
# Full development stack
composer run dev

# Frontend only
npm run dev
npm run build

# Production build
npm run build && composer install --optimize-autoloader
```

### Testing Commands
```bash
# Run all tests
php artisan test --compact

# Run single test file
php artisan test --compact tests/Feature/ExampleTest.php

# Run specific test method
php artisan test --compact --filter=testName

# Run with coverage
php artisan test --coverage
```

### Linting/Formatting
```bash
# PHP code formatting
vendor/bin/pint --dirty

# Fix all formatting issues
vendor/bin/pint
```

## Code Style Guidelines

### PHP Code Style (Main Project)

#### File Structure
- Use descriptive filenames in English (e.g., `CommentController.php`, `post_view.php`)
- Controllers in `/controllers/` directory
- Models in `/models/` directory
- Admin files in `/admin/` directory
- Shared components: `header.php`, `footer.php`, `db.php`

#### Naming Conventions
- **Classes**: PascalCase (e.g., `CommentController`, `CommentModel`)
- **Methods**: camelCase (e.g., `createComments`, `isAccessible`)
- **Variables**: camelCase with descriptive names (e.g., `post_id`, `author_name`)
- **Constants**: UPPER_SNAKE_CASE
- **Database tables**: snake_case (e.g., `comments`, `posts`)

#### PHP Code Standards
```php
<?php
// Always use opening PHP tag

// Use curly braces for control structures, even single lines
if ($condition) {
    // action
}

// Type declarations for parameters and return types
public function createComments(int $post_id, int $parent_id, string $author, string $content): bool
{
    // implementation
}

// Use prepared statements to prevent SQL injection
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ?");
$stmt->execute([$post_id]);
```

#### Security Guidelines
- **SQL Injection**: Always use prepared statements with PDO
- **XSS Prevention**: Use `htmlspecialchars()` for output
- **CSRF**: Use tokens for form submissions
- **Session Security**: Regenerate session IDs on login/logout
- **Input Validation**: Validate and sanitize all user inputs

#### Error Handling
```php
// Database connection with proper error handling
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("資料庫連線失敗，請稍後再試。");
}
```

#### Comments and Documentation
- Use Traditional Chinese for comments (following existing codebase)
- PHPDoc blocks for classes and methods
- Inline comments only for complex logic

```php
/**
 * 評論控制器 (Comment Controller)
 * 
 * 處理來自前端的評論提交請求，驗證資料並存入資料庫
 */
class CommentController
{
    /**
     * 建立新評論
     * 
     * @param int $post_id 文章ID
     * @param int $parent_id 父評論ID
     * @param string $author 作者名稱
     * @param string $content 評論內容
     * @return bool 成功回傳true，失敗回傳false
     */
    public function createComments(int $post_id, int $parent_id, string $author, string $content): bool
    {
        // implementation
    }
}
```

### HTML/CSS Guidelines

#### HTML Structure
- Use HTML5 semantic tags
- Include `header.php` and `footer.php` in all pages
- Responsive design with Tailwind CSS
- Meta tags for viewport and charset

#### CSS/Tailwind Guidelines
- Use Tailwind CSS classes via CDN
- Custom CSS in `<style>` blocks in `header.php`
- Consistent color scheme:
  - Primary: `#0d1a26` (bg-primary)
  - Secondary: `#1a2b3c` (bg-secondary)
  - Accent: `#ef4444` (btn-accent)
- Responsive design with mobile-first approach

#### JavaScript Guidelines
- Use vanilla JavaScript (no jQuery unless necessary)
- Event delegation for dynamic content
- AJAX for form submissions
- Error handling for API calls

```javascript
// Example AJAX call
fetch('/api/comment', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // success handling
    } else {
        // error handling
    }
})
.catch(error => {
    console.error('Error:', error);
});
```

### Database Guidelines

#### Table Structure
- Use `id` as primary key (auto-increment)
- `created_at` and `updated_at` timestamps
- Foreign keys with proper naming (e.g., `post_id`)
- Use `InnoDB` engine for foreign key support

#### SQL Conventions
- Use prepared statements exclusively
- Snake_case for column names
- Proper indexing for frequently queried columns
- Use transactions for multiple operations

```php
// Example model method
public function createComments(int $post_id, int $parent_id, string $author, string $content): bool
{
    $sql = "INSERT INTO comments (post_id, parent_id, author, content, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$post_id, $parent_id, $author, $content]);
}
```

## URL Structure and Routing

### Main Project URL Patterns
```apache
# Category pages
/category/photo     -> category.php?type=photo
/category/blog     -> category.php?type=blog
/category/photo/page/2 -> category.php?type=photo&page=2

# Post pages
/post/123          -> post_view.php?id=123
/post/123/slug     -> post_view.php?id=123

# Admin pages
/admin/post_manager.php
/admin/post_create.php
/admin/post_edit.php
```

### Laravel Project URL Patterns
- Follow Laravel RESTful conventions
- Use named routes: `route('posts.show', $post)`
- API versioning for APIs: `/api/v1/`

## File Organization

### Main Project Structure
```
/
├── index.php              # Homepage
├── header.php             # Shared header
├── footer.php             # Shared footer
├── db.php                 # Database connection
├── .htaccess              # URL rewriting rules
├── controllers/           # MVC controllers
├── models/                # MVC models
├── admin/                 # Admin panel files
├── uploads/               # File uploads directory
├── post/                  # Post-related files
└── category.php           # Category listing page
```

### Laravel Project Structure
- Follow Laravel 12 conventions
- Use `bootstrap/app.php` for configuration
- Models in `app/Models/`
- Controllers in `app/Http/Controllers/`
- Tests in `tests/Feature/` and `tests/Unit/`

## Development Workflow

### Main Project Development
1. Create/modify PHP files
2. Test URL rewriting in browser
3. Check database operations
4. Verify security measures
5. Test responsive design

### Laravel Project Development
1. Use `php artisan make:` commands for new files
2. Run `vendor/bin/pint --dirty` for formatting
3. Write and run tests
4. Use `composer run dev` for development
5. Check browser logs for frontend issues

## Security Checklist

### Main Project Security
- [ ] All database queries use prepared statements
- [ ] User inputs are sanitized with `htmlspecialchars()`
- [ ] File uploads are validated and secured
- [ ] Session management is secure
- [ ] Error messages don't expose sensitive information

### Laravel Project Security
- [ ] Use Laravel's built-in authentication
- [ ] Implement CSRF protection
- [ ] Use Laravel's validation rules
- [ ] Proper authorization with gates/policies
- [ ] Environment variables for configuration

## Common Issues and Solutions

### Main Project Issues
- **URL rewriting not working**: Check `.htaccess` and Apache mod_rewrite
- **Database connection failed**: Verify credentials in `db.php`
- **Session not working**: Ensure `session_start()` before output
- **CSS not loading**: Check Tailwind CDN link in `header.php`

### Laravel Project Issues
- **Vite manifest error**: Run `npm run build`
- **Migration failed**: Check database configuration
- **Test failing**: Use `php artisan test --filter=testName`
- **Permission denied**: Check storage directory permissions

## Best Practices

### Code Quality
- Write clean, readable code with proper indentation
- Use meaningful variable and function names
- Follow DRY (Don't Repeat Yourself) principle
- Implement proper error handling

### Performance
- Use database indexes for frequently queried columns
- Optimize images for web
- Minimize HTTP requests
- Use caching where appropriate

### Maintainability
- Keep code modular and reusable
- Document complex logic
- Use version control (Git)
- Follow consistent coding standards

This AGENTS.md file should be updated as the project evolves and new patterns emerge.