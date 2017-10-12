<?php

namespace App\Http\Controllers\admin;

use Carbon;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function masterList()
    {
        $users = User::select('id', 'firstname', 'lastname','email', 'banned_at')->with('profile')->where('login_type', 'STANDARD')->get();
        // dd($users);
        return view('admin.users.master-list', compact('users'));
    }

    public function banUser($id)
    {   
        User::whereId($id)->update(['banned_at' => now()]);

        return redirect(route('admin.manage-users'));
    }

    public function unbanUser($id)
    {
        User::whereId($id)->update(['banned_at' => null]);

        return redirect(route('admin.manage-users'));
    }

    public function destroy($id)
    {
        User::whereId($id)->delete();

        return redirect(route('admin.manage-users'));
    }
    //
}
