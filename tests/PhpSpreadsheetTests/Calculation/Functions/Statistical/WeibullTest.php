<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class WeibullTest extends TestCase
{
    /**
     * @dataProvider providerWEIBULL
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed $alpha
     * @param mixed $beta
     * @param mixed $cumulative
     */
    public function testWEIBULL($expectedResult, $value, $alpha, $beta, $cumulative): void
    {
        $result = Statistical::WEIBULL($value, $alpha, $beta, $cumulative);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerWEIBULL(): array
    {
        return require 'tests/data/Calculation/Statistical/WEIBULL.php';
    }
}
