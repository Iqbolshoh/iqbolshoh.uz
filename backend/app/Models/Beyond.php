<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beyond extends Model
{
    protected $fillable = ['icon', 'title', 'description', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'description' => 'array'];
    }
}
