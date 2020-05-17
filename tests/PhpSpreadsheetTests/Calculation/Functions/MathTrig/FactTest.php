<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FactTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFACT
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFACT($expectedResult, $value)
    {
        $result = MathTrig::FACT($value);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFACT()
    {
        return require 'tests/data/Calculation/MathTrig/FACT.php';
    }
}
