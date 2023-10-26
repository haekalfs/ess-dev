<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News_feed extends Model
{
    use HasFactory;
    protected $table = "news_feed";
    protected $fillable = ['id', 'title', 'date_released', 'created_by', 'content', 'img'];
}
