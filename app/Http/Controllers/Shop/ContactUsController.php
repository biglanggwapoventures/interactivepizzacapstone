<?php

namespace App\Http\Controllers\shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactUsController extends Controller
{
    public function showContactUs()
    {
        return view('shop.contact-us');
        # code...
    }
    //
}
