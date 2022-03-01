<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class NTest extends TestCase
{
    public function testNNoArgument(): void
    {
        $result = Functions::n();
        self::assertSame(0, $result);
    }

    /**
     * @dataProvider providerN
     *
     * @param mixed $expectedResult
     * @param number|string $value
     */
    public function testN($expectedResult, $value): void
    {
        $result = Functions::n($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public function providerN(): array
    {
        return require 'tests/data/Calculation/Information/N.php';
    }
}
