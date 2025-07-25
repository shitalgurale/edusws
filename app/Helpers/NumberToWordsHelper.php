<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    public static function convertNumberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
            6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
            11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen',
            15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'forty',
            50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty',
            90 => 'ninety', 100 => 'hundred', 1000 => 'thousand',
            1000000 => 'million', 1000000000 => 'billion'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . self::convertNumberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convertNumberToWords($remainder);
                }
                break;
            default:
                foreach (array_reverse($dictionary, true) as $value => $word) {
                    if ($number >= $value) {
                        $number_of_units = (int)($number / $value);
                        $remainder = $number % $value;
                        $string = self::convertNumberToWords($number_of_units) . ' ' . $word;
                        if ($remainder) {
                            $string .= $separator . self::convertNumberToWords($remainder);
                        }
                        break;
                    }
                }
                break;
        }

        if ($fraction !== null) {
            $string .= $decimal;
            foreach (str_split($fraction) as $digit) {
                $string .= $dictionary[$digit] . ' ';
            }
        }

        return ucfirst(trim($string));
    }

    public static function convertDateToWords($dateStr)
    {
        $date = \DateTime::createFromFormat('d-m-Y', $dateStr);
        if (!$date) return 'Invalid Date';

        $day = self::convertNumberToWords((int)$date->format('d'));
        $month = $date->format('F');
        $year = self::convertNumberToWords((int)$date->format('Y'));

        return ucfirst("$day $month $year");
    }
}
