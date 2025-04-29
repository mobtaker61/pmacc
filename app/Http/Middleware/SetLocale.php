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
        //dd('SetLocale middleware executed', Session::get('locale'), App::getLocale());

        Log::debug('Session locale: ' . json_encode(Session::all()));

        $sessionLocale = Session::get('locale');
        $cookieLocale = $request->cookie('locale');
        $defaultLocale = config('app.locale');
        $supportedLocales = ['fa' => 'fa', 'tr' => 'tr'];

        $targetLocale = in_array($sessionLocale, ['fa', 'tr']) ? $sessionLocale : (in_array($cookieLocale, ['fa', 'tr']) ? $cookieLocale : $defaultLocale);

        if ($sessionLocale === 'fa' || $sessionLocale === 'tr') {
            $targetLocale = $sessionLocale;
            Log::debug('Using locale from session: ' . $targetLocale);
        } else {
            Log::debug('No valid locale in session. Using default: ' . $targetLocale);
            // Session::put('locale', $targetLocale);
        }

        // Set the application locale for the current request
        App::setLocale($targetLocale);
        Log::debug('Application locale set to: ' . App::getLocale());

        // برای تست
        Log::info('SetLocale middleware', [
            'session_locale' => $sessionLocale,
            'target_locale' => $targetLocale,
            'app_locale' => App::getLocale(),
        ]);

        // Optional: Set locale for URL generation if needed elsewhere, though we avoid it in URLs
        // URL::defaults(['locale' => App::getLocale()]);
        
        return $next($request);
    }
} 