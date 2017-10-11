<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Pizza;

class HomeController extends Controller
{
    public function showHome()
    {
        $pizzas = Pizza::has('sizes')->get()->each->getIngredients();

        return view('shop.home', [
            'pizzas' => $pizzas,
        ]);
    }
}
