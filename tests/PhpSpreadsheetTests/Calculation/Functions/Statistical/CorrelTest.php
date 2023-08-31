<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CorrelTest extends TestCase
{
    /**
     * @dataProvider providerCORREL
     *
     * @param mixed $expectedResult
     * @param mixed $xargs
     * @param mixed $yargs
     */
    public function testCORREL($expectedResult, $xargs, $yargs): void
    {
        $result = Statistical\Trends::CORREL($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerCORREL(): array
    {
        return require 'tests/data/Calculation/Statistical/CORREL.php';
    }
}
