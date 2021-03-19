<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarATest extends TestCase
{
    /**
     * @dataProvider providerVARA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARA($expectedResult, $values): void
    {
        $result = Statistical::VARA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARA()
    {
        return require 'tests/data/Calculation/Statistical/VARA.php';
    }
}
