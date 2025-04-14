<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format a number with thousands separator and optional decimal places
     *
     * @param float|int $number The number to format
     * @param int $decimals Number of decimal places (default: 0)
     * @return string Formatted number
     */
    public static function format($number, $decimals = 0)
    {
        if (is_null($number) || $number === '') {
            return '';
        }

        // Convert to float to handle large numbers
        $number = (float) $number;

        // Format the number with thousands separator
        return number_format($number, $decimals, '.', ',');
    }

    /**
     * Parse a formatted number string back to a float
     *
     * @param string $formattedNumber The formatted number string
     * @return float The parsed number
     */
    public static function parse($formattedNumber)
    {
        if (empty($formattedNumber)) {
            return 0;
        }

        // Remove thousands separators and convert to float
        return (float) str_replace(',', '', $formattedNumber);
    }
} 