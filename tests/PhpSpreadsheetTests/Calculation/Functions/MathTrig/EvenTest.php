<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class EvenTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testEVEN($expectedResult, $value)
    {
        $result = MathTrig::EVEN($value);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerEVEN()
    {
        return require 'data/Calculation/MathTrig/EVEN.php';
    }
}
