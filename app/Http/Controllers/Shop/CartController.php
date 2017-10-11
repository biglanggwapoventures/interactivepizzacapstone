<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MyCart;
use Session;

class CartController extends Controller
{
    public function showCart()
    {
        $premadePizzas = MyCart::getPremade();
        $customPizzas = MyCart::getCustom();
        return view('shop.cart', [
            'premadePizzas' => $premadePizzas,
            'customPizzas' => $customPizzas,
            'total' => $premadePizzas->sum('total_amount') + $customPizzas->sum('total_amount'),
        ]);
    }

    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:CUSTOM,PREMADE',
            'id' => 'required',
        ]);

        MyCart::remove($validated['item_type'], $validated['id']);

        Session::flash('cartMessage', 'You cart has been updated successfully!');

        return response()->json([
            'result' => true,
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:CUSTOM,PREMADE',
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        MyCart::updateQuantity($validated['item_type'], $validated['id'], $validated['quantity']);

        Session::flash('cartMessage', 'You cart has been updated successfully!');

        return response()->json([
            'result' => true,
        ]);
    }

}
