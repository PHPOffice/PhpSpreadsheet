<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MMultTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMMULT
     *
     * @param mixed $expectedResult
     */
    public function testMMULT($expectedResult, ...$args)
    {
        $result = MathTrig::MMULT(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMMULT()
    {
        return require 'data/Calculation/MathTrig/MMULT.php';
    }
}
