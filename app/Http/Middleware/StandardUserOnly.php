<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class StandardUserOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is('standard')) {
            return $next($request);
        }
        return redirect(route('shop.show.home'));
    }
}
