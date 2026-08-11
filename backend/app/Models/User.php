<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function interruptions(): HasMany
    {
        return $this->hasMany(Interruption::class);
    }

    /**
     * Named `sentNotifications` rather than `notifications`: the Notifiable
     * trait already owns that name for Laravel's own database notifications,
     * and silently shadowing it would break password-reset delivery.
     */
    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function telegramAccounts(): HasMany
    {
        return $this->hasMany(TelegramAccount::class);
    }

    public function planSetting(): HasOne
    {
        return $this->hasOne(PlanSetting::class);
    }

    public function forecastReports(): HasMany
    {
        return $this->hasMany(ForecastReport::class);
    }
}
