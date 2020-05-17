<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class QuotientTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerQUOTIENT
     *
     * @param mixed $expectedResult
     */
    public function testQUOTIENT($expectedResult, ...$args)
    {
        $result = MathTrig::QUOTIENT(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerQUOTIENT()
    {
        return require 'tests/data/Calculation/MathTrig/QUOTIENT.php';
    }
}
