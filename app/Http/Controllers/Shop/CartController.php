<?php

namespace App\Http\Controllers\Shop;

use App\Beverage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'orderedBeverages' => MyCart::getBeverages(),
            'total' => $premadePizzas->sum('total_amount') + $customPizzas->sum('total_amount'),
            'errors' => MyCart::getErrorsFromCustomPizzas(),
            'beverages' => Beverage::all(),
        ]);
    }

    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:CUSTOM,PREMADE,BEVERAGE',
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
            'item_type' => 'required|in:CUSTOM,PREMADE,BEVERAGE',
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        MyCart::updateQuantity($validated['item_type'], $validated['id'], $validated['quantity']);

        Session::flash('cartMessage', 'You cart has been updated successfully!');

        return response()->json([
            'result' => true,
        ]);
    }

    public function updateBeverages(Request $request)
    {
        $validated = $this->validate($request, [
            'beverages.*.id' => [
                'required',
                Rule::exists('ingredients', 'id')->where(function ($query) {
                    $query->whereIsBeverage(1);
                }),
            ],
            'beverages.*.quantity' => 'nullable|integer|min:0',
        ]);

        $numAdded = collect($validated['beverages'])
            ->filter(function ($beverage) {
                return intval($beverage['quantity']) > 0;
            })
            ->each(function ($beverage) {
                MyCart::addBeverage($beverage['id'], $beverage['quantity']);
            })
            ->sum('quantity');

        // Session::flash();

        return redirect()
            ->back()
            ->with('cartMessage', "{$numAdded} item(s) has been added to cart!");

    }

}
