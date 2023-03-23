<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function test_convert_usd_to_eur_successful()
    {
        $price = 100;
        $expected  = 98;

        $this->assertEquals($expected, (new CurrencyService)->convert($price, "usd", "eur"));
    }

    public function test_convert_usd_to_gbp_returns_zero()
    {
        $price = 100;
        $expected  = 0;

        $this->assertEquals($expected, (new CurrencyService)->convert($price, "usd", "gbp"));
    }
}
