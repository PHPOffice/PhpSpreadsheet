<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class Atan2Test extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     * @param mixed $x
     * @param mixed $y
     */
    public function testATAN2($expectedResult, $x, $y): void
    {
        $result = MathTrig::ATAN2($x, $y);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerATAN2()
    {
        return require 'tests/data/Calculation/MathTrig/ATAN2.php';
    }
}
