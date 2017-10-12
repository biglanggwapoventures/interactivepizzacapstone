<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Ingredient;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return redirect(route('admin.manage-orders'));
    }

    public function addItemStock(Request $request)
    {
        $post = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|integer|min:1',
        ]);

        Ingredient::whereId($post['ingredient_id'])->increment('remaining_quantity', $post['quantity']);

        return response()->json([
            'result' => true,
        ]);
    }
}
