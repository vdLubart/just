<?php

namespace Lubart\Just\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Lubart\Just\Models\User;

class MasterAccess
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
        if(!User::authAsMaster()){
            return redirect('settings/noaccess');
        }

        return $next($request);
    }
}
