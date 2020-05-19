<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class AcotTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerACOT
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testACOT($expectedResult, $number): void
    {
        $result = MathTrig::ACOT($number);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerACOT()
    {
        return require 'tests/data/Calculation/MathTrig/ACOT.php';
    }
}
