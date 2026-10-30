<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_verified_at',
        'country_code',
        'photo',
        'password',
        'user_type',
        'blocked',
        'country',
        'registration_status',
        'currency_id',
        'preferred_currency',
        'bank_detail_id',
        'pickup_comission_persentage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id', 'id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class, 'user_id', 'id');
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'user_id', 'id')->where('is_default', true);
    }

    public function dispatcher()
    {
        return $this->hasOne(Dispatcher::class, 'user_id', 'id');
    }

    public function agent()
    {
        return $this->hasOne(Agent::class, 'user_id', 'id');
    }

    public function courier()
    {
        return $this->hasOne(Courier::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'id');
    }

    public function mobile()
    {
        return $this->hasOne(MobileAppUser::class, 'user_id', 'id');
    }

    public function kyc()
    {
        return $this->hasOne(Kyc::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function vtuTransactions()
    {
        return $this->hasMany(VtuTransaction::class);
    }

    public function deliveryPayouts()
    {
        return $this->hasMany(DeliveryManPayout::class, 'delivery_man_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'delivery_man_id', 'id');
    }

    public function activeVehicles()
    {
        return $this->vehicles()->where('status', Vehicle::STATUS_ACTIVE);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'permission' => $this->permission,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function bankDetail()
    {
        return $this->belongsTo(BankDetail::class, 'bank_detail_id');
    }

    public function currency()
    {
        return $this->belongsTo(CurrencyExchangeRate::class, 'currency_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function hasVerifiedContact(): bool
    {
        return (bool) ($this->email_verified_at || $this->phone_verified_at);
    }
}
