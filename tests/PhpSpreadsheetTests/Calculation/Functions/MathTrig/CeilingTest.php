<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class CeilingTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     */
    public function testCEILING($expectedResult, ...$args): void
    {
        $result = MathTrig::CEILING(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCEILING()
    {
        return require 'tests/data/Calculation/MathTrig/CEILING.php';
    }
}
