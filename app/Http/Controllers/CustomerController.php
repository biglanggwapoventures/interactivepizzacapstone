<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Order;
use App\Profile;
use App\User;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $criterias = [
        'customer' => ['customer_id', '='],
        'start_date' => ['order_date', '>='],
        'end_date' => ['order_date', '<='],
        'transaction_code' => ['transaction_code', '='],
        'type' => ['order_type', '='],
        'status' => ['order_status', '='],
    ];

    public function showOrderHistory(Request $request)
    {
        $orders = Order::owned();

        $search = collect($this->criterias)->each(function ($item, $key) use ($orders, $request) {
            $orders->when($request->has($key) && strlen(trim($request->{$key})), function ($orders) use ($key, $item, $request) {
                list($column, $operand) = $item;
                $orders->where($column, $operand, $request->{$key});
            });
        });

        $items = $orders->prepForMasterList();

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
        // $user = User::whereId(Auth::id())->with('profile')->first();
        $user = Auth::user()->load('profile');

        return view('shop.profile', [
            'user' => $user,
        ]);
    }

    // public function updateProfile(ProfileRequest $request, $id)
    public function updateProfile(ProfileRequest $request)
    {
        // $request->except('_token', 'password_confirmation');
        $user = Auth::user();

        // if ($request['old_password']) {
        if ($request->old_password) {

            // $user = User::whereId($id)->first();

            // if (Hash::check($request['old_password'], $user->password)) {
            if (Hash::check($request->old_password, $user->password)) {

                // User::whereId($id)->update([
                //     'password' => bcrypt($request['password']),
                // ]);
                $user->password = Hash::make($request->password);
                $user->save();

                return redirect(route('customer.show.profile'))->with('passUpdated', 'Your password has been successfuly updated!');
            } else {
                return redirect(route('customer.show.profile'))->with('passFail', "Old password doesn't match your current password.");
            }

        } else {
            DB::transaction(function () use ($request, $user) {

                // $user = $request->only('firstname', 'lastname', 'email');
                // $profile = $request->only('contact_number', 'barangay', 'street_number', 'city');

                $user->update($request->only('firstname', 'lastname', 'email'));

                $user->profile()->update($request->only('contact_number', 'barangay', 'street_number', 'city'));
                // Profile::whereUserId($id)->update($profile);

            }, 3);

            return redirect(route('customer.show.profile'))->with('profileUpdated', 'Your account has been successfuly updated!');
        }

    }
}
