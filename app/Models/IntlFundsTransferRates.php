<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntlFundsTransferRates extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        's_country',
        'rx_country',
        's_currency',
        'rx_currency',
        'calc',
        'commision',
        'ex_rate',
        'min_amt',
        'max_amt',
        'status'
    ];
}
