<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferIntent extends Model
{
    use HasFactory;

    protected $table = 'transfer_intent';
    protected $fillable = [
        'user_id',
        'intent_id',
        'payment_intent_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
