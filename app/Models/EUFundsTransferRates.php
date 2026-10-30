<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EUFundsTransferRates extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        's_country',
        'rx_country',
        'calc',
        'commision',
        'ex_rate',
        'min_amt',
        'max_amt',
        'status'
    ];
}
