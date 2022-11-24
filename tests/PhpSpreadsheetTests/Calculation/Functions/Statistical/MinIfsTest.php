<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run in spreadsheet context.
class MinIfsTest extends TestCase
{
    /**
     * @dataProvider providerMINIFS
     *
     * @param mixed $expectedResult
     */
    public function testMINIFS($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::MINIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMINIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/MINIFS.php';
    }
}
