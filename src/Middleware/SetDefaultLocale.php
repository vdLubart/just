<?php

namespace Just\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetDefaultLocale
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
        $locale = Session::get('locale');
        $defaultLocale = env('DEFAULT_LANG', 'en');

        if(!is_null($locale) and $locale !== $defaultLocale){
            return redirect("/" . $locale . $request->getRequestUri());
        }

        Session::put('locale', $defaultLocale);
        App::setLocale($defaultLocale);

        rebuildTranslationCache();

        return $next($request);
    }
}
