<?php

namespace Just\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class CatchLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        App::setLocale(Session::get('locale') ?? env('DEFAULT_LANG', 'en') );

        return $next($request);
    }
}
