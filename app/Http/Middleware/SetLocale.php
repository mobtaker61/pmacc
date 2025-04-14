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
        // dd('SetLocale Middleware Reached. Session Locale: ' . Session::get('locale', 'Not Set')); // Remove dd()

        $sessionLocale = Session::get('locale');
        $defaultLocale = config('app.locale');
        $supportedLocales = config('laravellocalization.supportedLocales', []);

        $targetLocale = $defaultLocale; // Start with default

        if ($sessionLocale && array_key_exists($sessionLocale, $supportedLocales)) {
            $targetLocale = $sessionLocale;
            Log::debug('Using locale from session: ' . $targetLocale);
        } else {
            Log::debug('No valid locale in session. Using default: ' . $targetLocale);
            // Optionally ensure the default locale is stored in session if it wasn't there
            // Session::put('locale', $targetLocale);
        }

        // Set the application locale for the current request
        App::setLocale($targetLocale);
        Log::debug('Application locale set to: ' . App::getLocale());

        // Optional: Set locale for URL generation if needed elsewhere, though we avoid it in URLs
        // URL::defaults(['locale' => App::getLocale()]);

        return $next($request);
    }
} 