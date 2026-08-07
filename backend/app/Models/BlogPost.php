<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = ['slug', 'title', 'excerpt', 'image', 'date', 'tags', 'featured', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'excerpt' => 'array', 'tags' => 'array', 'featured' => 'boolean', 'date' => 'date:Y-m-d'];
    }
}
