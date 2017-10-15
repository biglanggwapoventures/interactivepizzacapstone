<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Auth;
use App\User;
use App\Order;
use App\Profile;
use App\Http\Requests\ProfileRequest;

class CustomerController extends Controller
{
    public function showOrderHistory()
    {
        $items = Order::owned()->prepForMasterList();
        return view('shop.customer-order-history', [
            'items' => $items,
        ]);
    }

    public function showOrderDetails($order)
    {
        $order = Order::owned()->whereId($order);

        if (!$order->exists()) {
            abort(404);
        }

        $order = $order->detailed()->first();

        return view('shop.view-order-details', [
            'order' => $order,
        ]);
    }

    public function showProfile()
    {
        $user = User::whereId(Auth::id())->with('profile')->first();

        return view('shop.profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(ProfileRequest $request, $id)
    {
           $request->except('_token', '_method', 'password_confirmation');
            
            if($request['old_password']){

                $user = User::whereId($id)->first();

                if(Hash::check($request['old_password'], $user->password)){

                    User::whereId($id)->update([
                        'password' => bcrypt($request['password'])
                    ]);

                    return redirect(route('customer.show.profile'))->with('passUpdated', 'Your password has been successfuly updated!');
                }else{
                    return redirect(route('customer.show.profile'))->with('passFail', "Old password doesn't match your current password.");
                }
                 
            }else{
                DB::transaction(function () use ($request, $id){

                    $user = $request->only('firstname', 'lastname', 'email');
                    $profile = $request->only('contact_number', 'barangay', 'street_number', 'city');

                    $userSync = User::whereId($id)->update($user);
                    Profile::whereUserId($id)->update($profile);

                }, 3);

                return redirect(route('customer.show.profile'))->with('profileUpdated', 'Your account has been successfuly updated!');
            }
        
    }
}
