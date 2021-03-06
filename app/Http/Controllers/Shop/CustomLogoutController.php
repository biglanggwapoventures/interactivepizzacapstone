<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Auth;

class CustomLogoutController extends Controller
{
    public function __invoke()
    {
        Auth::logout();

        return redirect(route('shop.show.home'))->with([
            'result' => true,
        ]);
    }
}
