<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\IngredientCategory as Category;
use Illuminate\Http\Request;
use MyCart;
use Session;

class CustomPizzaController extends Controller
{
    public function showForm(Request $request)
    {
        // return;
        $preset = $request->has('id') ? MyCart::get($request->id, 'CUSTOM') : null;
        $categories = Category::has('ingredients')->with('ingredients')->customOrderSequenced()->get();
        return view('shop.build-custom-pizza', [
            'categories' => $categories,
            'preset' => $preset,
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|array',
            'item.*.category_id' => 'required|exists:ingredient_categories,id',
            'item.*.items' => 'required|array',
            'item.*.items.*' => 'required|exists:ingredients,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:SMALL,MEDIUM,LARGE',
            'remarks' => '',
        ]);
        MyCart::addCustomOrder($validated);
        Session::flash('cartMessage', "{$validated['quantity']} item(s) has been added to cart!");
        return response()->json([
            'result' => true,
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|array',
            'item.*.category_id' => 'required|exists:ingredient_categories,id',
            'item.*.items' => 'required|array',
            'item.*.items.*' => 'required|exists:ingredients,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:SMALL,MEDIUM,LARGE',
            'remarks' => '',
            'id' => 'required',
        ]);
        MyCart::updateCustomOrder($validated);
        Session::flash('cartMessage', 'You cart has been updated successfully!');
        return response()->json([
            'result' => true,
        ]);
    }
}
