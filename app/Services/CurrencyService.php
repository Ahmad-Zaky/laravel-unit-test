<?php

namespace App\Services;

class CurrencyService
{
    const RATES = [
        "usd" => [
            "eur" => 0.98
        ]
    ];

    public function convert(float $amount, string $from, string $to) 
    {
        $rate = self::RATES[$from][$to] ?? 0;

        return round($amount * $rate, 2);
    }
}