<?php

namespace App\Http\ViewComposers;

use App\Order;
use Illuminate\View\View;

class PendingOrdersComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('pendingCount', Order::pending()->count());
    }
}
