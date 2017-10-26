<?php

namespace App;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const VAT_VALUE = 0.12;

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
            $size = strtolower($detail->size);
            return $detail->quantity * $detail->usedIngredients->sum("ingredients.custom_unit_price_{$size}");
        });

        $beveragesTotal = $this->beverages->sum(function ($beverage) {
            return $beverage->unit_price * $beverage->pivot->quantity;
        });

        $this->total_amount = collect([$premadeTotal, $customTotal, $beveragesTotal])->sum();

        return $this->total_amount;
    }

    public function totalAmountWithoutVAT()
    {
        return $this->getTotalAmount() - $this->getVAT();
    }

    public function getVAT()
    {
        return $this->getTotalAmount() * self::VAT_VALUE;
    }

    public function scopeDetailed($query)
    {
        $query->with(['premadePizzaOrderDetails.pizzaSize.pizza', 'customPizzaOrder.usedIngredients.ingredients', 'customer.profile', 'deliveryPersonnel']);
    }

    public function scopePrepForMasterList($query)
    {
        return $query->orderBy('created_at', 'DESC')
            ->with(['premadePizzaOrderDetails.pizzaSize', 'customPizzaOrder.usedIngredients.ingredients', 'beverages'])
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

    public function beverages()
    {
        return $this->belongsToMany('App\Beverage', 'ordered_beverages', 'order_id', 'beverage_id')->withPivot('quantity');
    }

    public function decrementBeverageStocks()
    {
        $beverages = DB::table('ordered_beverages AS ob')
            ->where('ob.order_id', '=', $this->id);

        if ($beverages->exists()) {
            $beverages->join('ingredients AS i', 'i.id', '=', 'ob.beverage_id')
                ->update([
                    'i.remaining_quantity' => DB::raw('i.remaining_quantity - ob.quantity'),
                ]);
        }
        return true;

    }

    public function checkAvailability()
    {
        $ingredients = [];
        $lacking = [];

        $premade = DB::table('premade_pizza_order_details AS ppod')->whereOrderId($this->id);
        if ($premade->exists()) {

            $premade = $premade->addSelect(DB::raw('pi.ingredient_id AS id, SUM(pi.quantity * ppod.quantity) AS needed'))
                ->rightJoin('pizza_ingredients AS pi', 'pi.pizza_size_id', '=', 'ppod.pizza_size_id')
                ->groupBy('pi.ingredient_id')
                ->get();

            if ($premade->isNotEmpty()) {
                $premade->each(function ($item) use (&$ingredients) {
                    if (isset($ingredients[$item->id])) {
                        $ingredients[$item->id] += intval($item->needed);
                    } else {
                        $ingredients[$item->id] = intval($item->needed);
                    }
                });
            }
        }

        $custom = DB::table('custom_pizza_orders AS cpo')->whereOrderId($this->id);
        if ($custom->exists()) {
            $custom = $custom->addSelect(DB::raw('i.id, SUM(CASE WHEN cpo.size = "SMALL" THEN i.custom_quantity_needed_small * cpo.quantity WHEN cpo.size = "MEDIUM" THEN i.custom_quantity_needed_medium * cpo.quantity ELSE i.custom_quantity_needed_large * cpo.quantity END) AS needed'))
                ->join('custom_pizza_order_details AS cpod', 'cpod.custom_pizza_order_id', '=', 'cpo.id')
                ->join('ingredients AS i', 'i.id', '=', 'cpod.ingredient_id')
                ->groupBy('i.id')
                ->get();

            if ($custom->isNotEmpty()) {
                $custom->each(function ($item) use (&$ingredients) {
                    if (isset($ingredients[$item->id])) {
                        $ingredients[$item->id] += $item->needed;
                    } else {
                        $ingredients[$item->id] = $item->needed;
                    }
                });
            }
        }

        $stocks = DB::table('ingredients')->select('id', 'remaining_quantity', 'description')->get();

        foreach ($stocks as $stock) {
            if (isset($ingredients[$stock->id]) && ($offset = intval($stock->remaining_quantity) - intval($ingredients[$stock->id])) < 0) {
                $lacking[] = "{$stock->description} needs {$ingredients[$stock->id]}, has {$stock->remaining_quantity}";
            }
        }

        return $lacking;

    }
}
