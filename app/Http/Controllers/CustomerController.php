<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $criterias = [
        'customer' => ['customer_id', '='],
        'start_date' => ['order_date', '>='],
        'end_date' => ['order_date', '<='],
        'transaction_code' => ['transaction_code', '='],
        'type' => ['order_type', '='],
        'status' => ['order_status', '='],
    ];

    public function showOrderHistory(Request $request)
    {
        $orders = Order::owned();

        $search = collect($this->criterias)->each(function ($item, $key) use ($orders, $request) {
            $orders->when($request->has($key) && strlen(trim($request->{$key})), function ($orders) use ($key, $item, $request) {
                list($column, $operand) = $item;
                $orders->where($column, $operand, $request->{$key});
            });
        });

        $items = $orders->prepForMasterList();

        return view('shop.customer-order-history', [
            'items' => $items,
        ]);
    }

    public function showOrderDetails($order)
    {
        $order = Order::owned()->whereId($order);

        if (!$order->exists()) {
            abort(404);
        }

        $order = $order->detailed()->first();

        return view('shop.view-order-details', [
            'order' => $order,
        ]);
    }

    public function showProfile()
    {

    }
}
