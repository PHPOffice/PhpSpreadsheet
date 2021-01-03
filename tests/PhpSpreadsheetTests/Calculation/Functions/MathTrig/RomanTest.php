<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class RomanTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     */
    public function testROMAN($expectedResult, ...$args): void
    {
        $result = MathTrig::ROMAN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return require 'tests/data/Calculation/MathTrig/ROMAN.php';
    }
}
