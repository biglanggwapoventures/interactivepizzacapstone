<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Order;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use MyCart;
use Session;

class OrderPizzaController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*.pizza_size_id' => 'nullable|present|integer|exists:pizza_sizes,id',
            'order.*.quantity' => 'nullable|present|integer|min:1',
        ], [
            'order.required' => 'Please choose at least 1 (one) pizza to add to cart!',
            'order.array' => 'Please choose at least 1 (one) pizza to add to cart!',
        ]);

        $numAdded = collect($validated['order'])
            ->filter(function ($order) {
                return intval($order['quantity']) > 0;
            })
            ->each(function ($order) {
                MyCart::addOrder($order);
            })
            ->sum('quantity');

        Session::flash('cartMessage', "{$numAdded} item(s) has been added to cart!");
        return response()->json([
            'result' => true,
        ]);
    }

    public function confirmOrder(Request $request)
    {
        DB::transaction(function () use ($request) {

            $total = MyCart::getTotal();

            $totalOrderCount = MyCart::pizzaCount();

            $timeOffset = $totalOrderCount < 5 ? '30 minutes' : '1 hour';
            $minPickup = date_create()->modify("+ {$timeOffset}")->format('H:i');

            $validated = $request->validate([
                'order_type' => 'required|in:DELIVERY,PICKUP',
                'recipient' => 'required_if:order_type,PICKUP',
                'pickup_time' => "nullable|required_if:order_type,PICKUP|date_format:H:i|after:{$minPickup}",
                // 'estimated_delivery_time' => 'nullable|required_if:order_type,DELIVERY|date_format:"h:i A"',
                'cash_amount' => "nullable|required_if:order_type,DELIVERY|numeric|min:{$total}",
                'destination_type' => 'nullable|required_if:order_type,DELIVERY|in:CITY_PROPER,OUTSIDE_CITY',
                'street' => 'required_if:order_type,DELIVERY',
                'barangay' => 'required_if:order_type,DELIVERY',
                'city' => 'required_if:order_type,DELIVERY',
                'landmark' => 'required_if:order_type,DELIVERY',
                'agreement' => '',
            ], [
                'pickup_time.after' => "We are sorry but you need to wait at least {$timeOffset} for your order..",
            ]);

            $order = Order::create([
                'order_type' => $validated['order_type'],
                'transaction_code' => str_random(10),
                'order_date' => date('Y-m-d'),
                'customer_id' => Auth::id(),
            ]);

            if ($order->is('pickup')) {
                $order->pickup()->create([
                    'recipient' => $validated['recipient'],
                    'estimated_pickup_time' => Carbon::createFromFormat('H:i', $validated['pickup_time'])->format('H:i:s'),
                ]);
            } elseif ($order->is('delivery')) {
                $deliveryDetails = array_only($validated, [
                    'cash_amount',
                    'destination_type',
                    'landmark',
                    'street',
                    'barangay',
                    'city',
                ]);
                // $deliveryDetails += [
                //     'estimated_delivery_time' => Carbon::createFromFormat('h:i A', $validated['estimated_delivery_time'])->format('H:i:s'),
                // ];
                $order->delivery()->create($deliveryDetails);
            }
            MyCart::savePremadePizzaTo($order);
            MyCart::saveCustomPizzaTo($order);
            MyCart::saveBeveragesTo($order);
            MyCart::clear();
        });

        return redirect(route('customer.show.order-history'))->with('messageFromCart', 'A new order has been successully placed!');
    }

}
