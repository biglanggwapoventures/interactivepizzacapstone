<?php

namespace App\Http\Controllers\admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminLogoutController extends Controller
{
    public function __invoke()
    {
        Auth::logout();

        return redirect(route('shop.show.home'))->with([
            'result' => true,
        ]);
    }
    //
}
