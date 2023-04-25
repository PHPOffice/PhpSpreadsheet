<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class NTest extends TestCase
{
    public function testNNoArgument(): void
    {
        $result = Value::asNumber();
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
        $result = Value::asNumber($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerN(): array
    {
        return require 'tests/data/Calculation/Information/N.php';
    }
}
