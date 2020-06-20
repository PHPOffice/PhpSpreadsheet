<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class TruncTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     */
    public function testTRUNC($expectedResult, ...$args): void
    {
        $result = MathTrig::TRUNC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTRUNC()
    {
        return require 'tests/data/Calculation/MathTrig/TRUNC.php';
    }
}
