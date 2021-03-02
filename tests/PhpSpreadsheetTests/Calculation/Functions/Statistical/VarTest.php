<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarTest extends TestCase
{
    /**
     * @dataProvider providerVAR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVAR($expectedResult, $values): void
    {
        $result = Statistical::VARFunc($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVAR()
    {
        return require 'tests/data/Calculation/Statistical/VAR.php';
    }
}
