<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
// 告訴 Laravel 這個 Model 對應的資料庫資料表名稱是 posts
    protected $table = 'posts';

    // 如果你的舊資料表沒有 created_at 和 updated_at 這兩個欄位，
    // 請加上下面這行，否則 Laravel 會噴錯。
    public $timestamps = false;
}
