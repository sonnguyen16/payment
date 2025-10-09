<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        '', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín',
        'mười', 'mười một', 'mười hai', 'mười ba', 'mười bốn', 'mười lăm',
        'mười sáu', 'mười bảy', 'mười tám', 'mười chín'
    ];

    private static $tens = [
        '', '', 'hai mười', 'ba mười', 'bốn mười', 'năm mười',
        'sáu mười', 'bảy mười', 'tám mười', 'chín mười'
    ];

    private static $scales = [
        '', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'Không đồng';
        }

        if ($number < 0) {
            return 'Âm ' . self::convert(-$number);
        }

        $words = self::convertGroup($number);
        
        return ucfirst(trim($words)) . ' đồng';
    }

    private static function convertGroup($number)
    {
        if ($number == 0) {
            return '';
        }

        if ($number < 20) {
            return self::$ones[$number];
        }

        if ($number < 100) {
            $tens = intval($number / 10);
            $ones = $number % 10;
            
            if ($tens == 1) {
                return self::$ones[$number];
            }
            
            $result = self::$tens[$tens];
            if ($ones == 1 && $tens > 1) {
                $result .= ' mốt';
            } elseif ($ones == 5 && $tens > 1) {
                $result .= ' lăm';
            } elseif ($ones > 0) {
                $result .= ' ' . self::$ones[$ones];
            }
            
            return $result;
        }

        if ($number < 1000) {
            $hundreds = intval($number / 100);
            $remainder = $number % 100;
            
            $result = self::$ones[$hundreds] . ' trăm';
            
            if ($remainder > 0) {
                if ($remainder < 10) {
                    $result .= ' lẻ ' . self::$ones[$remainder];
                } else {
                    $result .= ' ' . self::convertGroup($remainder);
                }
            }
            
            return $result;
        }

        // Handle larger numbers
        $scaleIndex = 0;
        $result = '';
        
        while ($number > 0) {
            $group = $number % 1000;
            
            if ($group > 0) {
                $groupWords = self::convertGroup($group);
                if ($scaleIndex > 0) {
                    $groupWords .= ' ' . self::$scales[$scaleIndex];
                }
                
                if ($result) {
                    $result = $groupWords . ' ' . $result;
                } else {
                    $result = $groupWords;
                }
            }
            
            $number = intval($number / 1000);
            $scaleIndex++;
        }
        
        return $result;
    }
}
