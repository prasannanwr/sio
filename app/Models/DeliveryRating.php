<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRating extends Model
{
    use HasFactory;

    public const LABELS = [
        5 => 'Excellent',
        4 => 'Good',
        3 => 'Average',
        2 => 'Below Average',
        1 => 'Poor',
    ];

    protected $fillable = [
        'order_id',
        'delivery_man_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public static function label(?int $rating): ?string
    {
        return self::LABELS[$rating] ?? null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
