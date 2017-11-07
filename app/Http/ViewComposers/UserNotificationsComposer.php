<?php

namespace App\Http\ViewComposers;

use Auth;
use Illuminate\View\View;

class UserNotificationsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $notifications = Auth::user()->notifications();
            $view->with('unreadNotificationsCount', $notifications->whereIsRead(0)->count());
            $view->with('notifications', $notifications->get());
        }

    }
}
