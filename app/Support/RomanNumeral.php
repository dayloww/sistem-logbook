<?php

namespace App\Support;

class RomanNumeral
{
    private const MAP = [
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1,
    ];

    public static function convert(int $value): string
    {
        $result = '';

        foreach (self::MAP as $symbol => $number) {
            while ($value >= $number) {
                $result .= $symbol;
                $value -= $number;
            }
        }

        return $result;
    }
}
