<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchorderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }   
}
