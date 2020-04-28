<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CombinTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOMBIN
     *
     * @param mixed $expectedResult
     */
    public function testCOMBIN($expectedResult, ...$args)
    {
        $result = MathTrig::COMBIN(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOMBIN()
    {
        return require 'data/Calculation/MathTrig/COMBIN.php';
    }
}
