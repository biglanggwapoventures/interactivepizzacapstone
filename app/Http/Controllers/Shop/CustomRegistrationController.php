<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;

class CustomRegistrationController extends Controller
{
    public function showForm($value = '')
    {
        return view('shop.registration');
    }

    public function doRegister(Request $request)
    {
        DB::transaction(function () use (&$request) {

            $validated = $this->validate($request, [
                'firstname' => 'required|max:255',
                'lastname' => 'required|max:255',
                'email' => 'required|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
                'contact_number' => 'required|max:255',
                'barangay' => 'required|max:255',
                'street_number' => 'required|max:255',
                'city' => 'required|max:255',
            ]);

            $postUser = array_only($validated, ['firstname', 'lastname', 'email']);
            $postUser += [
                'login_type' => 'STANDARD',
                'password' => Hash::make($request->password),
            ];

            $user = User::create($postUser);

            $postProfile = array_only($validated, ['contact_number', 'street_number', 'barangay', 'city']);

            $user->profile()->create($postProfile);

            Auth::login($user);

        }, 3);

        return redirect(route('shop.show.home'))->with('notification', [
            'result' => true,
            'message' => 'You have successfully created an account!',
        ]);
    }
}
