<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class QuartileTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerQUARTILE
     *
     * @param mixed $expectedResult
     */
    public function testQUARTILE($expectedResult, ...$args): void
    {
        $result = Statistical::QUARTILE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerQUARTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/QUARTILE.php';
    }
}
