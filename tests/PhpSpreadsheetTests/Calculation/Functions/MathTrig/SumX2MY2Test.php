<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SumX2MY2Test extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMX2MY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMX2MY2($expectedResult, ...$args): void
    {
        $result = MathTrig::SUMX2MY2(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUMX2MY2()
    {
        return require 'tests/data/Calculation/MathTrig/SUMX2MY2.php';
    }
}
