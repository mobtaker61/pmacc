<?php

namespace App\Helpers;

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Log;

class DateHelper
{
    public static function format($date, $format = null)
    {
        if (is_null($date)) {
            return '-';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (app()->getLocale() === 'fa') {
            $jDate = Jalalian::fromCarbon($date);
            return $jDate->format($format ?? 'Y/m/d');
        }

        // For Turkish locale
        if (app()->getLocale() === 'tr') {
            return $date->format($format ?? 'd.m.Y');
        }

        // Default format for other locales
        return $date->format($format ?? 'Y-m-d');
    }
    
    /**
     * Parse a date string to Carbon instance based on current locale
     *
     * @param string $dateString The date string to parse
     * @return Carbon|null Carbon instance or null if parsing fails
     */
    public static function parse($dateString)
    {
        if (empty($dateString)) {
            Log::warning('DateHelper::parse called with empty date string');
            return null;
        }
        
        Log::debug('DateHelper::parse attempting to parse date: ' . $dateString . ' for locale: ' . app()->getLocale());
        
        try {
            if (app()->getLocale() === 'fa') {
                // Try common Persian date formats
                $formats = ['Y/m/d', 'Y/n/j', 'Y/n/d', 'Y/m/j'];
                
                foreach ($formats as $format) {
                    try {
                        Log::debug('Trying Persian format: ' . $format . ' for date: ' . $dateString);
                        $jDate = Jalalian::fromFormat($format, $dateString);
                        $carbonDate = $jDate->toCarbon();
                        Log::debug('Successfully parsed Persian date to: ' . $carbonDate->format('Y-m-d'));
                        return $carbonDate;
                    } catch (\Exception $e) {
                        Log::debug('Failed with format ' . $format . ': ' . $e->getMessage());
                        continue;
                    }
                }
                
                // If we couldn't parse with Jalalian formatters, see if it's already a Gregorian date
                Log::debug('Attempting to parse as Gregorian date');
                throw new \Exception('Could not parse as Persian date, trying fallback');
                
            } elseif (app()->getLocale() === 'tr') {
                // Try common Turkish date formats
                $formats = ['d.m.Y', 'j.n.Y', 'd/m/Y', 'j/n/Y'];
                
                foreach ($formats as $format) {
                    try {
                        Log::debug('Trying Turkish format: ' . $format . ' for date: ' . $dateString);
                        $carbonDate = Carbon::createFromFormat($format, $dateString);
                        Log::debug('Successfully parsed Turkish date to: ' . $carbonDate->format('Y-m-d'));
                        return $carbonDate;
                    } catch (\Exception $e) {
                        Log::debug('Failed with format ' . $format . ': ' . $e->getMessage());
                        continue;
                    }
                }
                
                // If Turkish formats fail, try standard formats
                Log::debug('Attempting to parse as standard date');
                throw new \Exception('Could not parse as Turkish date, trying fallback');
            }
            
            // For other locales or fallback, try standard formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d'];
            foreach ($formats as $format) {
                try {
                    Log::debug('Trying standard format: ' . $format . ' for date: ' . $dateString);
                    $carbonDate = Carbon::createFromFormat($format, $dateString);
                    Log::debug('Successfully parsed standard date to: ' . $carbonDate->format('Y-m-d'));
                    return $carbonDate;
                } catch (\Exception $e) {
                    Log::debug('Failed with format ' . $format . ': ' . $e->getMessage());
                    continue;
                }
            }
            
            // Last resort: Use Carbon's flexible parser
            Log::debug('Using Carbon::parse as last resort');
            $carbonDate = Carbon::parse($dateString);
            Log::debug('Successfully parsed with Carbon::parse to: ' . $carbonDate->format('Y-m-d'));
            return $carbonDate;
            
        } catch (\Exception $e) {
            Log::error('Date parsing error: ' . $e->getMessage() . ' for date: ' . $dateString . ' and locale: ' . app()->getLocale());
            
            // Final fallback attempt
            try {
                Log::debug('Final fallback with Carbon::parse');
                $carbonDate = Carbon::parse($dateString);
                Log::debug('Fallback successful: ' . $carbonDate->format('Y-m-d'));
                return $carbonDate;
            } catch (\Exception $e2) {
                Log::error('All parsing methods failed: ' . $e2->getMessage());
                return null;
            }
        }
    }
} 