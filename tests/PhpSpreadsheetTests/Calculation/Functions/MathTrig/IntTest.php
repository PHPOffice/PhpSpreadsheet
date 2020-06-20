<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class IntTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINT
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testINT($expectedResult, $value): void
    {
        $result = MathTrig::INT($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return require 'tests/data/Calculation/MathTrig/INT.php';
    }
}
