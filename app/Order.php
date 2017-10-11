<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'transaction_code',
        'customer_id',
        'order_type',
        'order_date',
        'remarks',
        'order_status',
    ];

    protected $appends = [
        // 'total_amount',
        'next_status',
    ];

    public static function statuses()
    {
        return [
            'PICKUP' => collect(['PENDING', 'PROCESSING', 'READY_FOR_PICKUP', 'RECEIVED']),
            'DELIVERY' => collect(['PENDING', 'PROCESSING', 'DELIVERING', 'RECEIVED']),
        ];
    }

    public function getNextStatusAttribute()
    {
        $status = self::statuses();
        $currentStatus = $status[$this->order_type]->search($this->order_status);
        return $currentStatus !== ($status[$this->order_type]->count() - 1) ? $status[$this->order_type]->get($currentStatus + 1) : $this->order_status;
    }

    public function isNotReceived()
    {
        return !$this->isReceived();
    }

    public function isReceived()
    {
        $status = self::statuses();
        $currentStatus = $status[$this->order_type]->search($this->order_status);
        return $currentStatus === ($status[$this->order_type]->count() - 1);
    }

    public function isSetToBe($orderStatus)
    {
        return strtolower($orderStatus) === strtolower($this->next_status);
    }

    public function is($orderType)
    {
        return strtolower($orderType) === strtolower($this->order_type);
    }

    public function pickup()
    {
        return $this->hasOne('App\Pickup', 'order_id');
    }

    public function delivery()
    {
        return $this->hasOne('App\Delivery', 'order_id');
    }

    public function premadePizzaOrderDetails()
    {
        return $this->hasMany('App\PremadePizzaOrderDetail', 'order_id');
    }

    public function customPizzaOrder()
    {
        return $this->hasMany('App\CustomPizzaOrder', 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id');
    }

    public function getTotalAmount()
    {
        if (property_exists($this, 'total_amount')) {
            return $this->total_amount;
        }

        $premadeTotal = $this->premadePizzaOrderDetails->sum(function ($detail) {
            return $detail->quantity * $detail->pizzaSize->unit_price;
        });

        $customTotal = $this->customPizzaOrder->sum(function ($detail) {
            return $detail->quantity * $detail->usedIngredients->sum('ingredients.unit_price');
        });

        $total = $premadeTotal - ($premadeTotal * 0.12);

        $this->total_amount = $total;

        return $total;
    }

    public function totalAmountWithoutVAT()
    {
        return $this->getTotalAmount() - $this->getVAT();
    }

    public function getVAT()
    {
        return $this->getTotalAmount() * 0.12;
    }

    public function scopeDetailed($query)
    {
        $query->with(['premadePizzaOrderDetails.pizzaSize.pizza', 'customPizzaOrder.usedIngredients.ingredients', 'customer.profile', 'deliveryPersonnel']);
    }

    public function scopePrepForMasterList($query)
    {
        return $query->orderBy('created_at', 'DESC')
            ->with(['premadePizzaOrderDetails.pizzaSize', 'customPizzaOrder.usedIngredients.ingredients'])
            ->get()
            ->each
            ->getTotalAmount();
    }

    public function scopeOwned($query)
    {
        return $query->whereCustomerId(Auth::id());
    }

    public function deliveryPersonnel()
    {
        return $this->belongsTo('App\DeliveryPersonnel');
    }
}
