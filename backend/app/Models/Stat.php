<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = ['icon', 'value', 'label', 'sort_order'];

    protected function casts(): array
    {
        return ['label' => 'array'];
    }
}
