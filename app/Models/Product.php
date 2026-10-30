<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_ar',
        'name_it',
        'name_ch',
        'name_fr',
        'name_dt',
        'name_pt',
        'description',
        'description_ar',
        'description_it',
        'description_ch',
        'description_fr',
        'description_dt',
        'description_pt',
        'price_per_tonne',
        'price_per_kg',
        'origin_port',
        'supported_ports',
        'shipping_terms',
        'image',
    ];
}
