<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['category', 'icon', 'price', 'title', 'description', 'tech', 'features', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'description' => 'array', 'tech' => 'array', 'features' => 'array'];
    }
}
