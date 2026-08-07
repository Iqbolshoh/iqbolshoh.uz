<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    protected $fillable = ['year', 'title', 'description', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'description' => 'array'];
    }
}
