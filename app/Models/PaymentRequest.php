<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Fill;
use Stripe\Payout;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_detail_id',
        'reciept_attachement',
        'amount',
        'status',
        'user_id',
        'admin_id',
    ];


    public function bankDetail()
    {
        return $this->belongsTo(BankDetail::class, 'bank_detail_id');
    }

}
