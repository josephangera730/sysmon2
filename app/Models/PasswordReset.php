<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
    ];

    // Cek apakah token sudah expired (misal 60 menit)
    public function isExpired()
    {
        return Carbon::parse($this->created_at)->addMinutes(60)->isPast();
    }
}
