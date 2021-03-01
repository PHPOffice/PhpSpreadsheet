<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevTest extends TestCase
{
    /**
     * @dataProvider providerSTDEV
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEV($expectedResult, $values): void
    {
        $result = Statistical::STDEV($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEV()
    {
        return require 'tests/data/Calculation/Statistical/STDEV.php';
    }
}
