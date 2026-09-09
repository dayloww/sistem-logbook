<?php

namespace App\Support;

final class Percent
{
    public static function format(float $value): string
    {
        return abs($value - round($value)) < 0.0001
            ? (string) round($value)
            : number_format($value, 1, ',', '.');
    }

    public static function signed(float $value): string
    {
        $formatted = self::format($value);

        return $value > 0 ? '+'.$formatted : $formatted;
    }
}