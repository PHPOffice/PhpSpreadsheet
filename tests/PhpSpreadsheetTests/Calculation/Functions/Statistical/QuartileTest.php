<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class QuartileTest extends TestCase
{
    /**
     * @dataProvider providerQUARTILE
     *
     * @param mixed $expectedResult
     */
    public function testQUARTILE($expectedResult, ...$args): void
    {
        $result = Statistical\Percentiles::QUARTILE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerQUARTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/QUARTILE.php';
    }
}
