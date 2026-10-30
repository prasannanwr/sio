<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatcher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'phone_alt',
        'address1',
        'address2',
        'zip',
        'city',
        'state',
        'country',
        'location_id',
        'agency_type',
        'business_name',
        'tax_id_code',
        'vat_no',
        'pec',
        'sdi',
        'city_id',
        'state_id',
        'country_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
