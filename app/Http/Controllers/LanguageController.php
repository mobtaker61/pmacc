<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        Log::info('Language switch requested from ' . App::getLocale() . ' to ' . $locale);
        
        // Check if the locale is valid
        $supportedLocales = config('laravellocalization.supportedLocales', []);
        if (array_key_exists($locale, $supportedLocales)) {
            // Save to session
            Session::put('locale', $locale);
            Session::save(); // Ensure session is saved immediately
            // Log the value immediately after putting it in session
            Log::info('Locale put in session. Value now: ' . Session::get('locale')); 
            
            // Temporarily disable cookie setting for testing
            // Cookie::queue('locale', $locale, 60*24*30); 
            
            // Flash a success message
            $languageName = $supportedLocales[$locale]['name'];
            Session::flash('success', __('Language changed to :language', ['language' => $languageName]));
            
            Log::info('Language switch successful - Session locale: ' . Session::get('locale'));
            
            // For debugging details
            Log::debug('Session data dump: ' . json_encode(Session::all()));
            // Log::debug('Cookie data: ' . $locale);
            
            Cookie::queue('locale', $locale, 60*24*30); // 30 روز
        } else {
            Log::warning('Invalid locale requested: ' . $locale);
            Session::flash('error', __('Invalid language selected'));
        }

        // Redirect back (without cookie in this test)
        // return Redirect::back()->withCookie(cookie('locale', $locale, 60*24*30)); 
        return Redirect::back();
    }
    
    public function test()
    {
        $data = [
            'session_locale' => Session::get('locale'),
            'app_locale' => App::getLocale(),
            'config_locale' => config('app.locale'),
            'supported_locales' => config('laravellocalization.supportedLocales'),
            'session_all' => Session::all(),
            'cookies' => request()->cookies->all(),
            'request_headers' => request()->headers->all(),
            'route' => request()->route()->getName()
        ];
        
        return response()->json($data);
    }
} 