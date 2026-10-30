<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EUFundTransferOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'walk_in_customer_id',
        'customer_id',
        'dispatcher_id',
        'e_u_funds_transfer_rate_id',
        'rx_surname',
        'rx_name',
        'rx_phone',
        'rx_email',
        'rx_bank_name',
        'rx_bank_routing_no',
        'rx_bank_swift_code',
        'rx_bank_account_name',
        'rx_bank_account_number',
        'rx_country',
        's_country',
        's_amount',
        'rx_amount',
        'rx_amount',
        'tx_status',
        'tx_reference',
        'tracking_id'
    ];

    public function walk_in_customer()
    {
        return $this->belongsTo(WalkInCustomer::class, 'walk_in_customer_id', 'id');
    }

    public function rate()
    {
        return $this->belongsTo(EUFundsTransferRates::class, 'e_u_funds_transfer_rate_id', 'id');
    }
}
