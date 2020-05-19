<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class FloorPreciseTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFLOORPRECISE
     *
     * @param mixed $expectedResult
     */
    public function testFLOOR($expectedResult, ...$args): void
    {
        $result = MathTrig::FLOORPRECISE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFLOORPRECISE()
    {
        return require 'tests/data/Calculation/MathTrig/FLOORPRECISE.php';
    }
}
