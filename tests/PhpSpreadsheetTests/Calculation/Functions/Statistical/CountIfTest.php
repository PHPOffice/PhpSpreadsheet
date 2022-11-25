<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

//TODO Run in spreadsheet content.
class CountIfTest extends TestCase
{
    /**
     * @dataProvider providerCOUNTIF
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIF($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::COUNTIF(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTIF(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIF.php';
    }
}
