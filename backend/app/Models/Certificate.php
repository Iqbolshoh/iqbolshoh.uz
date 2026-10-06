<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Anything worth showing on the Certificates page: certificates, diplomas,
 * awards, finished courses, letters of thanks.
 *
 * `issued_at` is deliberately left uncast: the admin form feeds the raw value
 * straight into an <input type="date">, which only accepts "Y-m-d".
 */
class Certificate extends Model
{
    public const TYPES = [
        'certificate' => 'Certificate',
        'diploma'     => 'Diploma',
        'award'       => 'Award',
        'course'      => 'Course',
        'letter'      => 'Letter of thanks',
        'other'       => 'Other',
    ];

    protected $fillable = ['title', 'description', 'issuer', 'type', 'issued_at', 'image', 'credential_url', 'sort_order'];

    protected function casts(): array
    {
        return ['title' => 'array', 'description' => 'array'];
    }
}
