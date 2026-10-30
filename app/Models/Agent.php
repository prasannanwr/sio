<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agency_type',
        'name',
        'phone',
        'phone_alt',
        'address1',
        'address2',
        'zip',
        'city_id',
        'state_id',
        'country_id',
        'status',
        'attachment_path',
        'location_id',
        'business_name',
        'tax_id_code',
        'vat_no',
        'pec',
        'sdi',
        'front_attachment'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function state(){
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}

