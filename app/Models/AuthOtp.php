<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthOtp extends Model
{
    public const PURPOSE_SIGNUP = 'signup';
    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_PHONE = 'phone';

    protected $fillable = [
        'user_id',
        'identifier',
        'channel',
        'purpose',
        'code_hash',
        'attempts',
        'expires_at',
        'consumed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
