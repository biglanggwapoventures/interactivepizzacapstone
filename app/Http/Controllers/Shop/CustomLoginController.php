<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Validator;

class CustomLoginController extends Controller
{
    public function showForm($value = '')
    {
        return view('shop.login');
    }

    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ], [
            'email.exists' => 'The :attribute you entered does not match any account.',
            'email.email' => 'Make sure you entered a valid :attribute',
        ]);

        if ($validator->passes() && Auth::attempt($request->only(['email', 'password']))) {
            if (Auth::user()->banned_at) {
                Auth::logout();
                $validator->errors()->add('email', 'Account blocked!');
            } else {
                return redirect(route('shop.show.home'));
            }
        } else {
            $validator->errors()->add('password', 'You entered an incorrect password');
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($validator);
    }
}
