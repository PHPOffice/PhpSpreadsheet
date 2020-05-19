<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FloorMathTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFLOORMATH
     *
     * @param mixed $expectedResult
     */
    public function testFLOORMATH($expectedResult, ...$args): void
    {
        $result = MathTrig::FLOORMATH(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFLOORMATH()
    {
        return require 'tests/data/Calculation/MathTrig/FLOORMATH.php';
    }
}
