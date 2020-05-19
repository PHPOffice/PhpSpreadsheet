<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SqrtPiTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testSQRTPI($expectedResult, $value): void
    {
        $result = MathTrig::SQRTPI($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSQRTPI()
    {
        return require 'tests/data/Calculation/MathTrig/SQRTPI.php';
    }
}
