<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'length',
        'width',
        'height',
        'weight',
        'qty',
        'item_desc',
        'item_value'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
