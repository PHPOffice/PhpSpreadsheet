<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class OddTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerODD
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testODD($expectedResult, $value): void
    {
        $result = MathTrig::ODD($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerODD()
    {
        return require 'tests/data/Calculation/MathTrig/ODD.php';
    }
}
