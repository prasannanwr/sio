<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryManPayoutLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_man_payout_id',
        'payout_reference',
        'action',
        'performed_by',
        'old_status',
        'new_status',
        'message',
        'payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function payout()
    {
        return $this->belongsTo(DeliveryManPayout::class, 'delivery_man_payout_id', 'id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by', 'id');
    }
}
