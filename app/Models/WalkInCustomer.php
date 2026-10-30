<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalkInCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'name',
        'birthDate',
        'birthPlace',
        'gender',
        'belfioreCode',
        'doc_type',
        'doc_num',
        'doc_front',
        'doc_back',
        'tax_code',
        'phone',
        'email',
        'address',
        'kyc_status',
        'company_name'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'walk_in_customer_id', 'id');
    }
}
