<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PizzaIngredientsRequest;
use App\Pizza;
use App\PizzaSize;
use DB;
use Pizza as PizzaHelper;

class PizzaIngredientsController extends Controller
{
    public function save(Pizza $pizza, PizzaIngredientsRequest $request)
    {
        DB::transaction(function () use (&$request, &$pizza) {

            $input = $request->only(['size', 'unit_price']);

            $pizzaSize = $pizza->sizes()->whereSize($request->size);

            $ingredients = collect($request->item)->mapWithKeys(function ($ingredient) {
                return [$ingredient['id'] => ['quantity' => $ingredient['quantity']]];
            });

            if ($pizzaSize->exists()) {

                $size = $pizzaSize->first();

                $size->fill($input)->save();

                $size->ingredients()->sync($ingredients);

            } else {

                $size = $pizza->sizes()->create($input);

                $size->ingredients()->attach($ingredients);

            }

        }, 3);

        return redirect()->route('pizzas.index');
    }

    public function showForm(Pizza $pizza, $size)
    {
        if (!PizzaHelper::sizes()->contains($size)) {
            abort(404);
        }

        $pizzaSize = $pizza->sizes()->whereSize($size)->first() ?: new PizzaSize;

        return view('admin.pizzas.manage-ingredients', [
            'pizza' => $pizza,
            'size' => $pizzaSize->load('ingredients'),
            'selectedSize' => $size,
        ]);
    }
}
