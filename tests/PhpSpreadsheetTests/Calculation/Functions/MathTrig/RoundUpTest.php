<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class RoundUpTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROUNDUP
     *
     * @param mixed $expectedResult
     */
    public function testROUNDUP($expectedResult, ...$args): void
    {
        $result = MathTrig::ROUNDUP(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerROUNDUP()
    {
        return require 'tests/data/Calculation/MathTrig/ROUNDUP.php';
    }
}
