<?php

namespace Just\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check() and !Auth::user()->isActive){
            return redirect('settings/noaccess');
        }

        return $next($request);
    }
}
