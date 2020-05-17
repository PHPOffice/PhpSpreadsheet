<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FloorTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFLOOR
     *
     * @param mixed $expectedResult
     */
    public function testFLOOR($expectedResult, ...$args)
    {
        $result = MathTrig::FLOOR(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFLOOR()
    {
        return require 'tests/data/Calculation/MathTrig/FLOOR.php';
    }
}
