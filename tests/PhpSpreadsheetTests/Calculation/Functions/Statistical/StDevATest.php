<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevATest extends TestCase
{
    /**
     * @dataProvider providerSTDEVA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVA($expectedResult, $values): void
    {
        $result = Statistical::STDEVA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVA()
    {
        return require 'tests/data/Calculation/Statistical/STDEVA.php';
    }
}
