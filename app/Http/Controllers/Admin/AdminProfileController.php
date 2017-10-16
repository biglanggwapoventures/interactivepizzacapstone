<?php

namespace App\Http\Controllers\admin;

use DB;
use Hash;
use Auth;
use App\User;
use App\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;

class AdminProfileController extends Controller
{
    public function showProfile()
    {
        $user = User::whereId(Auth::id())->with('profile')->first();

        return view('admin.profile', [
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

                    return redirect(route('admin.show.profile'))->with('passUpdated', 'Your password has been successfuly updated!');
                }else{
                    return redirect(route('admin.show.profile'))->with('passFail', "Old password doesn't match your current password.");
                }
                 
            }else{
                DB::transaction(function () use ($request, $id){

                    $user = $request->only('firstname', 'lastname', 'email');
                    $profile = $request->only('contact_number', 'barangay', 'street_number', 'city');

                    $userSync = User::whereId($id)->update($user);
                    Profile::whereUserId($id)->update($profile);

                }, 3);

                return redirect(route('admin.show.profile'))->with('profileUpdated', 'Your account has been successfuly updated!');
            }
        
    }
    //
}
