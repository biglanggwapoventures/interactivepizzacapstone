<?php

namespace App\Http\Controllers;

class ShopController extends Controller
{
    public function showHome()
    {
        return redirect(route('shop.show.home'));
    }
}
