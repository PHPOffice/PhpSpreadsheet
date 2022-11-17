<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class SteyxTest extends TestCase
{
    /**
     * @dataProvider providerSTEYX
     *
     * @param mixed $expectedResult
     */
    public function testSTEYX($expectedResult, array $xargs, array $yargs): void
    {
        $result = Statistical\Trends::STEYX($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTEYX(): array
    {
        return require 'tests/data/Calculation/Statistical/STEYX.php';
    }
}
