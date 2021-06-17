<?php

namespace Just\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
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
        $locale = $request->segment(1);
        $defaultLocale = env('DEFAULT_LANG', 'en');
        Session::put('locale', $locale);

        if($locale === $defaultLocale){
            return redirect(str_replace("/" . $locale, "", $request->getRequestUri()));
        }

        App::setLocale($locale);

        rebuildTranslationCache();

        return $next($request);
    }
}
