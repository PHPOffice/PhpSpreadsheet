<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SecTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSEC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testSEC($expectedResult, $angle): void
    {
        $result = MathTrig::SEC($angle);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSEC()
    {
        return require 'tests/data/Calculation/MathTrig/SEC.php';
    }
}
