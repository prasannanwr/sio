<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'courier_id',
        'dispatcher_id',
        'batch_id',
        'status',
        'delivery_man_status',
        'pickup_location_id',
        'delivery_location_id',
        'current_location_id',
        'shipping_rate_id',
        'tracking_id',
        'pickup_time',
        'dispatcher_date',
        'delivery_time',
        'delivery_name',
        'delivery_phone',
        'delivery_email',
        'delivery_phone_alt',
        'delivery_address1',
        'delivery_address2',
        'delivery_zip',
        'delivery_city',
        'delivery_state',
        'delivery_country',
        'delivery_customer_id',
        'pickup_name',
        'pickup_phone',
        'pickup_email',
        'pickup_phone_alt',
        'pickup_address1',
        'pickup_address2',
        'pickup_zip',
        'pickup_city',
        'pickup_state',
        'pickup_country',
        'return_date',
        'cond_of_goods',
        'val_of_goods',
        'val_cur',
        'terms_of_sale',
        'customs_inv_num',
        'provider',
        'freight_mode',
        'shipping_cost',
        'shipping_currency',
        'current_location_country_id',
        'current_location_city_id',
        'pickup_location_country_id',
        'pickup_location_state_id',
        'pickup_location_city_id',
        'delivery_location_country_id',
        'delivery_location_state_id',
        'delivery_location_city_id',
        'invoice_document',
        'packing_list_document',
        'id_document',
        'permit_document',
        'agnet_id',
        'cummercial_invoice',
        'company_name',

    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // the customer recieving the package
    public function delivery_customer()
    {
        return $this->belongsTo(Customer::class, 'delivery_customer_id', '');
    }


    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function dispatcher()
    {
        return $this->belongsTo(Dispatcher::class);
    }

    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'dispatcher_id', 'id');
    }

    public function deliveryPayoutItems()
    {
        return $this->hasMany(DeliveryManPayoutItem::class, 'order_id', 'id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(DeliveryTrackingEvent::class, 'order_id', 'id');
    }

    public function batch()
    {
        return $this->belongsTo(OrderBatch::class, 'batch_id', 'id');
    }

    public function shipping_rate()
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id', 'id');
    }

    public function current_location()
    {
        return $this->belongsTo(Location::class, 'current_location_id', 'id');
    }

    public function pickup_location()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id', 'id');
    }

    public function delivery_location()
    {
        return $this->belongsTo(Location::class, 'delivery_location_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function walk_in_customer()
    {
        return $this->belongsTo(WalkInCustomer::class, 'walk_in_customer_id', 'id');
    }

    public function currentCountry()
    {
        return $this->belongsTo(Country::class, 'current_location_country_id');
    }

    public function currentCity()
    {
        return $this->belongsTo(City::class,'current_location_city_id');
    }

    public function pickupCountry()
    {
        return $this->belongsTo(Country::class, 'pickup_location_country_id');
    }

    public function pickupCity()
    {
        return $this->belongsTo(City::class,'pickup_location_city_id');
    }

    public function deliveryCountry()
    {
        return $this->belongsTo(Country::class, 'delivery_location_country_id');
    }

    public function deliveryCity()
    {
        return $this->belongsTo(City::class,'delivery_location_city_id');
    }
}
