<?php

declare(strict_types=1);

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
     */
    public function testN(mixed $expectedResult, mixed $value): void
    {
        $result = Value::asNumber($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerN(): array
    {
        return require 'tests/data/Calculation/Information/N.php';
    }
}
