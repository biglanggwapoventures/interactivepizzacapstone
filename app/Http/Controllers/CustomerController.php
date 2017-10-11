<?php

namespace App\Http\Controllers;

use App\Order;

class CustomerController extends Controller
{
    public function showOrderHistory()
    {
        $items = Order::owned()->prepForMasterList();
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
        # code...
    }
}
