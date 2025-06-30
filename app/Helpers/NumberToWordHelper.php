<?php

namespace App\Helpers;

class NumberToWordHelper
{
    protected static $words = [
        0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen',
        17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
        30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    ];

    public static function convert($number)
    {
        if (!is_numeric($number)) {
            return '';
        }

        $number = number_format((float)$number, 2, '.', ''); // format to 2 decimals

        $parts = explode('.', $number);

        $intPart = (int)$parts[0];
        $decimalPart = isset($parts[1]) ? (int)$parts[1] : 0;

        $words = self::convertNumber($intPart);

        if ($decimalPart > 0) {
            $words .= ' and ' . self::convertNumber($decimalPart) . ' Cents';
        }

        return $words;
    }

    protected static function convertNumber($number)
    {
        if ($number < 21) {
            return self::$words[$number] ?? '';
        }

        if ($number < 100) {
            $tens = floor($number / 10) * 10;
            $units = $number % 10;
            return self::$words[$tens] . ($units ? ' ' . self::$words[$units] : '');
        }

        if ($number < 1000) {
            $hundreds = floor($number / 100);
            $remainder = $number % 100;
            return self::$words[$hundreds] . ' Hundred' . ($remainder ? ' ' . self::convertNumber($remainder) : '');
        }

        if ($number < 1000000) {
            $thousands = floor($number / 1000);
            $remainder = $number % 1000;
            return self::convertNumber($thousands) . ' Thousand' . ($remainder ? ' ' . self::convertNumber($remainder) : '');
        }

        return (string)$number; // fallback
    }
}
