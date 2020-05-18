<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class ArabicTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerARABIC
     *
     * @param mixed $expectedResult
     * @param string $romanNumeral
     */
    public function testARABIC($expectedResult, $romanNumeral): void
    {
        $result = MathTrig::ARABIC($romanNumeral);
        self::assertEquals($expectedResult, $result);
    }

    public function providerARABIC()
    {
        return require 'tests/data/Calculation/MathTrig/ARABIC.php';
    }
}
