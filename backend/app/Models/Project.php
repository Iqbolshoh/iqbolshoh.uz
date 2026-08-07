<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'image', 'tech', 'live_demo', 'github', 'featured', 'category', 'sort_order'];

    protected function casts(): array
    {
        return ['name' => 'array', 'description' => 'array', 'tech' => 'array', 'featured' => 'boolean'];
    }
}
