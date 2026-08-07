<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'service_id',
        'service_name',
        'service_price',
        'ip',
        'user_agent',
        'read_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
