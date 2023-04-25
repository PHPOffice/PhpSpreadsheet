<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run test in spreadsheet context.
class TrendTest extends TestCase
{
    /**
     * @dataProvider providerGROWTH
     *
     * @param mixed $expectedResult
     */
    public function testTREND($expectedResult, array $yValues, array $xValues, ?array $newValues = null, ?bool $const = null): void
    {
        if ($newValues === null) {
            $result = Statistical\Trends::TREND($yValues, $xValues);
        } elseif ($const === null) {
            $result = Statistical\Trends::TREND($yValues, $xValues, $newValues);
        } else {
            $result = Statistical\Trends::TREND($yValues, $xValues, $newValues, $const);
        }

        self::assertEqualsWithDelta($expectedResult, $result[0], 1E-12);
    }

    public static function providerGROWTH(): array
    {
        return require 'tests/data/Calculation/Statistical/TREND.php';
    }
}
