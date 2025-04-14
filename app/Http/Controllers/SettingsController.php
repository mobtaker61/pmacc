<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function getTryRate()
    {
        try {
            $rate = Setting::where('key', 'try_to_irr_rate')->first();
            
            if (!$rate) {
                Log::warning('TRY rate setting not found in database');
                return response()->json([
                    'rate' => '1'
                ]);
            }

            return response()->json([
                'rate' => $rate->value
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting TRY rate: ' . $e->getMessage());
            return response()->json([
                'rate' => '1'
            ]);
        }
    }
} 