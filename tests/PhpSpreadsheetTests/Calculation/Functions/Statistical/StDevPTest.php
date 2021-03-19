<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevPTest extends TestCase
{
    /**
     * @dataProvider providerSTDEVP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVP($expectedResult, $values): void
    {
        $result = Statistical::STDEVP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVP()
    {
        return require 'tests/data/Calculation/Statistical/STDEVP.php';
    }
}
