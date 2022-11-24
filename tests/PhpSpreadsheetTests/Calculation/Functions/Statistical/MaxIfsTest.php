<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run in spreadsheet context.
class MaxIfsTest extends TestCase
{
    /**
     * @dataProvider providerMAXIFS
     *
     * @param mixed $expectedResult
     */
    public function testMAXIFS($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::MAXIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMAXIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXIFS.php';
    }
}
