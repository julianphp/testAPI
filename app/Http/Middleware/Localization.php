<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('lang');
        if (in_array($locale, config('app.locales'), true)){
            \App::setLocale($locale);
            session()->put('locale',$locale);
        } else {
            \App::setLocale(config('app.fallback_locale'));
            session()->put('locale',config('app.fallback_locale'));
        }


        return $next($request);
    }
}
