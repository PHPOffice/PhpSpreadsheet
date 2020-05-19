<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CscTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCSC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSC($expectedResult, $angle): void
    {
        $result = MathTrig::CSC($angle);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCSC()
    {
        return require 'tests/data/Calculation/MathTrig/CSC.php';
    }
}
