<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::debug('SetLocale middleware executing - Request Path: ' . $request->path());
        
        // Let LaravelLocalization handle the locale setting
        // We only handle cookie persistence here
        if ($request->hasCookie('locale')) {
            $cookieLocale = $request->cookie('locale');
            if (array_key_exists($cookieLocale, config('laravellocalization.supportedLocales', []))) {
                Log::debug('Found valid locale in cookie: ' . $cookieLocale);
                Session::put('locale', $cookieLocale);
                Session::save();
                Log::debug('Saved locale from cookie to session.');
            }
        }
        
        Log::debug('SetLocale finished. Locale in session (if set): ' . Session::get('locale'));
        
        return $next($request);
    }
} 