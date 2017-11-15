<?php

namespace App\Http\Controllers\API;

use App\DeliveryPersonnel;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Validator;

class PersonnelAPIController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|exists:delivery_personnels',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $personnel = DeliveryPersonnel::whereMobileNumber($request->mobile_number)->first();

        return response()->json([
            'result' => true,
            'data' => $personnel,
        ]);
    }

    public function getOrdersToBeDelivered($personnelId)
    {
        $orders = Order::whereDeliveryPersonnelId($personnelId)
            ->whereOrderStatus('DELIVERING')
            ->with(['delivery', 'customer'])
            ->prepForMasterList();

        return response()->json([
            'result' => true,
            'data' => $orders,
        ]);
    }
}
