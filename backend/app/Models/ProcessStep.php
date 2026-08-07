<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    protected $fillable = ['step', 'title', 'description', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'description' => 'array'];
    }
}
