<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFunds extends Model
{
    use HasFactory;

    protected $fillable = [
        'transId',
        'user_id',
        'amount',
        'currency',
        'description',
        'flag',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
