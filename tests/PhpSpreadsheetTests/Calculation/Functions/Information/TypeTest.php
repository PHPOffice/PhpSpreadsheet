<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testTypeNoArgument(): void
    {
        $result = Functions::TYPE();
        self::assertSame(1, $result);
    }

    /**
     * @dataProvider providerTYPE
     *
     * @param mixed $value
     */
    public function testTYPE(int $expectedResult, $value): void
    {
        $result = Functions::TYPE($value);
        self::assertSame($expectedResult, $result);
    }

    public function providerTYPE(): array
    {
        return require 'tests/data/Calculation/Information/TYPE.php';
    }
}
