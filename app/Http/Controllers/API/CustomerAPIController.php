<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Validator;

class CustomerAPIController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            $user = User::whereEmail($request->email)->first();
            if (Hash::check($request->password, $user->password)) {
                $user->load('profile');
                return response()->json([
                    'result' => true,
                    'data' => $user,
                ]);
            }

            $validator->errors()->add('password', 'Invalid password');
        }

        return response()->json([
            'result' => false,
            'errors' => $validator->errors(),
        ], 422);
    }
    public function getOrderHistory($customerId)
    {
        $orders = Order::whereCustomerId($customerId);

        $items = $orders->with('deliveryPersonnel')->prepForMasterList();

        return response()->json([
            'result' => true,
            'data' => $items,
        ]);
    }
}
