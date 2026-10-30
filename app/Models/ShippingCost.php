<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight',
        'country_name',
        'country_iso_2',
        'cost',
        'country_id'
    ];

    public function getDisplayLabelAttribute(): string
    {
        $country = $this->country_name ?: 'Unknown country';

        if ($this->country_iso_2 && stripos($country, $this->country_iso_2) === false) {
            $country .= ' (' . $this->country_iso_2 . ')';
        }

        $weight = $this->weight !== null ? $this->weight . ' kg' : 'Any weight';
        $cost = $this->cost !== null ? number_format((float) $this->cost, 2) : '0.00';

        return $country . ' | ' . $weight . ' | Cost: ' . $cost;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
