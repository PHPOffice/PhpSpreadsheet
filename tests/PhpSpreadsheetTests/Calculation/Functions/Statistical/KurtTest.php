<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class KurtTest extends TestCase
{
    /**
     * @dataProvider providerKURT
     *
     * @param mixed $expectedResult
     */
    public function testKURT($expectedResult, ...$args): void
    {
        $result = Statistical::KURT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerKURT(): array
    {
        return require 'tests/data/Calculation/Statistical/KURT.php';
    }
}
