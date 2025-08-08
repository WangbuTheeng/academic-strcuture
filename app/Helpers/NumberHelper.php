<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Convert a number to words.
     *
     * @param float $number
     * @return string
     */
    public static function numberToWords($number)
    {
        $number = (float) $number;
        $integerPart = (int) $number;
        $decimalPart = round(($number - $integerPart) * 100);
        
        $words = self::convertIntegerToWords($integerPart);
        
        if ($decimalPart > 0) {
            $words .= ' and ' . self::convertIntegerToWords($decimalPart) . ' paisa';
        }
        
        return $words . ' rupees';
    }
    
    /**
     * Convert integer to words.
     *
     * @param int $number
     * @return string
     */
    private static function convertIntegerToWords($number)
    {
        if ($number == 0) {
            return 'zero';
        }
        
        $ones = [
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        ];
        
        $tens = [
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        ];
        
        $scales = [
            '', 'thousand', 'million', 'billion', 'trillion'
        ];
        
        if ($number < 20) {
            return $ones[$number];
        }
        
        if ($number < 100) {
            return $tens[intval($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }
        
        if ($number < 1000) {
            return $ones[intval($number / 100)] . ' hundred' . ($number % 100 ? ' ' . self::convertIntegerToWords($number % 100) : '');
        }
        
        // Handle larger numbers
        $scaleIndex = 0;
        $result = '';
        
        while ($number > 0) {
            $chunk = $number % 1000;
            
            if ($chunk != 0) {
                $chunkWords = self::convertIntegerToWords($chunk);
                if ($scaleIndex > 0) {
                    $chunkWords .= ' ' . $scales[$scaleIndex];
                }
                
                if ($result) {
                    $result = $chunkWords . ' ' . $result;
                } else {
                    $result = $chunkWords;
                }
            }
            
            $number = intval($number / 1000);
            $scaleIndex++;
        }
        
        return $result;
    }
    
    /**
     * Format currency amount.
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function formatCurrency($amount, $currency = 'NRs.')
    {
        return $currency . ' ' . number_format($amount, 2);
    }
    
    /**
     * Convert number to Indian numbering system (lakhs, crores).
     *
     * @param float $number
     * @return string
     */
    public static function formatIndianCurrency($number)
    {
        $number = (float) $number;

        if ($number >= 10000000) { // 1 crore
            return 'NRs. ' . number_format($number / 10000000, 2) . ' crore';
        } elseif ($number >= 100000) { // 1 lakh
            return 'NRs. ' . number_format($number / 100000, 2) . ' lakh';
        } elseif ($number >= 1000) { // 1 thousand
            return 'NRs. ' . number_format($number / 1000, 2) . ' thousand';
        } else {
            return 'NRs. ' . number_format($number, 2);
        }
    }
    
    /**
     * Get ordinal suffix for a number.
     *
     * @param int $number
     * @return string
     */
    public static function getOrdinalSuffix($number)
    {
        $number = (int) $number;
        
        if ($number % 100 >= 11 && $number % 100 <= 13) {
            return $number . 'th';
        }
        
        switch ($number % 10) {
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
            default:
                return $number . 'th';
        }
    }
    
    /**
     * Calculate percentage.
     *
     * @param float $value
     * @param float $total
     * @param int $decimals
     * @return string
     */
    public static function calculatePercentage($value, $total, $decimals = 2)
    {
        if ($total == 0) {
            return '0.00%';
        }
        
        $percentage = ($value / $total) * 100;
        return number_format($percentage, $decimals) . '%';
    }
    
    /**
     * Format file size in human readable format.
     *
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function formatFileSize($bytes, $decimals = 2)
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}
