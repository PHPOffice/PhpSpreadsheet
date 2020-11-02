<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{
    /**
     * @dataProvider providerMAX
     *
     * @param mixed $expectedResult
     */
    public function testMAX($expectedResult, ...$args): void
    {
        $result = Statistical::MAX(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMAX(): array
    {
        return require 'tests/data/Calculation/Statistical/MAX.php';
    }
}
