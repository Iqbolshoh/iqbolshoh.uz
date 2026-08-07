<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    protected $fillable = ['icon', 'text', 'sort_order'];

    protected function casts(): array
    {
        return ['text' => 'array'];
    }
}
