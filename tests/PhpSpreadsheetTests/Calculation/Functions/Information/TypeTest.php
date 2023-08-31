<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testTypeNoArgument(): void
    {
        $result = Value::type();
        self::assertSame(1, $result);
    }

    /**
     * @dataProvider providerTYPE
     *
     * @param mixed $value
     */
    public function testTYPE(int $expectedResult, $value): void
    {
        $result = Value::type($value);
        self::assertSame($expectedResult, $result);
    }

    public static function providerTYPE(): array
    {
        return require 'tests/data/Calculation/Information/TYPE.php';
    }
}
