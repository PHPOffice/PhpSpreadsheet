<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run in spreadsheet context.
class CountIfsTest extends TestCase
{
    /**
     * @dataProvider providerCOUNTIFS
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIFS($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::COUNTIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIFS.php';
    }
}
