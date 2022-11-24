<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run in spreadsheet context.
class CovarTest extends TestCase
{
    /**
     * @dataProvider providerCOVAR
     *
     * @param mixed $expectedResult
     */
    public function testCOVAR($expectedResult, ...$args): void
    {
        $result = Statistical\Trends::COVAR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/COVAR.php';
    }
}
