<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'ref',
        'amt_expected',
        'amt_paid',
        'status',
        'misc'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
