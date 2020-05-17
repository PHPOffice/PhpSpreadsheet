<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MdeTermTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMDETERM
     *
     * @param mixed $expectedResult
     */
    public function testMDETERM($expectedResult, ...$args)
    {
        $result = MathTrig::MDETERM(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMDETERM()
    {
        return require 'tests/data/Calculation/MathTrig/MDETERM.php';
    }
}
