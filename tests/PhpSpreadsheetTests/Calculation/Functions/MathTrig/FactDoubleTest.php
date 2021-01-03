<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FactDoubleTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFACTDOUBLE
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFACTDOUBLE($expectedResult, $value): void
    {
        $result = MathTrig::FACTDOUBLE($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFACTDOUBLE()
    {
        return require 'tests/data/Calculation/MathTrig/FACTDOUBLE.php';
    }
}
