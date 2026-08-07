<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'ip', 'user_agent', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
