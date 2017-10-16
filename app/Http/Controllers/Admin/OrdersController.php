<?php

namespace App\Http\Controllers\Admin;

use App\DeliveryPersonnel;
use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class OrdersController extends Controller
{

    protected $criterias = [
        'customer' => ['customer_id', '='],
        'start_date' => ['order_date', '>='],
        'end_date' => ['order_date', '<='],
        'transaction_code' => ['transaction_code', '='],
        'type' => ['order_type', '='],
        'status' => ['order_status', '='],
    ];

    public function masterList(Request $request)
    {
        $orders = Order::select();

        $search = collect($this->criterias)->each(function ($item, $key) use ($orders, $request) {
            $orders->when($request->has($key) && strlen(trim($request->{$key})), function ($orders) use ($key, $item, $request) {
                list($column, $operand) = $item;
                $orders->where($column, $operand, $request->{$key});
            });
        });

        $items = $orders->prepForMasterList();
        $deliveryPersonnels = DeliveryPersonnel::toList()->prepend('** PLEASE SELECT A DELIVERY PERSONNEL **', '');
        $customers = User::standard()->get()->pluck('fullname', 'id')->prepend('** ALL CUSTOMERS **', '');

        return view('admin.orders.master-list', [
            'items' => $items,
            'deliveryPersonnels' => $deliveryPersonnels,
            'customers' => $customers,
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

                if ($order->isSetTobe('processing')) {
                    $order->customPizzaOrder->each->decrementStocks();
                    $order->premadePizzaOrderDetails->each->decrementStocks();
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
