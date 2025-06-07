<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title', // Blog post title
        'slug',  // SEO-friendly slug for URL
        'image_path', // Path to blog image
        'content', // Blog post body
    ];
}
