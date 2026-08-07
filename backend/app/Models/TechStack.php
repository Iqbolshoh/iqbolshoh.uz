<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechStack extends Model
{
    protected $fillable = ['name', 'icon', 'level', 'sort_order'];

    protected function casts(): array
    {
        return ['level' => 'integer'];
    }
}
