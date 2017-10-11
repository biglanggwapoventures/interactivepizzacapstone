<?php

namespace App\Http\Controllers\Admin;

use App\DeliveryPersonnel;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class OrdersController extends Controller
{
    public function masterList(Request $request)
    {
        $items = Order::prepForMasterList();
        $deliveryPersonnels = DeliveryPersonnel::toList()->prepend('** PLEASE SELECT A DELIVERY PERSONNEL **', '');

        return view('admin.orders.master-list', [
            'items' => $items,
            'deliveryPersonnels' => $deliveryPersonnels,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                Rule::exists('orders')->where(function ($query) {
                    $query->where('order_status', '!=', 'RECEIVED');
                }),
            ],
            'order_status' => 'required|in:PROCESSING,READY_FOR_PICKUP,RECEIVED,DELIVERING',
            'delivery_personnel_id' => 'nullable|required_if:order_status,DELIVERING|exists:delivery_personnels,id',
        ]);

        $order = Order::find($request->id);

        if ($validator->passes()) {
            if ($request->order_status != $order->next_status) {
                $validator->errors()->add('order_status', 'Cannot set status.');
            } else {
                if ($order->isSetToBe('delivering')) {
                    // dd($request->delivery_personnel_id);
                    $order->delivery_personnel_id = $request->delivery_personnel_id;
                }
                $order->order_status = $request->order_status;
                $order->save();
                return redirect()->back();
            }
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($validator);
    }

    public function showOrderDetails($orderId)
    {
        $order = Order::whereId($orderId);

        if (!$order->exists()) {
            abort(404);
        }

        $order = $order->detailed()->first();

        return view('admin.view-order', [
            'order' => $order,
        ]);
    }

}
