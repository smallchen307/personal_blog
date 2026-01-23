<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Post;
use Illuminate\Foundation\Application; // 為了抓版本資訊
use Illuminate\Support\Facades\Route;  // 為了判斷是否有登入路由



class PostController extends Controller
{
    public function index() { //建立一個叫index的方法，用首頁來處理邏輯
        
        //原本在web.php裡面的邏輯搬到這
        return Inertia::render('Welcome',[
            'canLogin' => true,
            'canRegister' => true,
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'posts' => Post::all(),
        ]);
    }

    public function post_view(Post $post) {
        // 廚師現在手上已經直接拿到這篇文章了 ($post)
        // 接下來只要把這盤菜交給前端的 Show.jsx 頁面
        return Inertia::render('Post/PostView', [
            'post' => $post
        ]);
    }
}