<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO run test in spreadsheet context
class LinEstTest extends TestCase
{
    /**
     * @dataProvider providerLINEST
     *
     * @param mixed $xValues
     * @param mixed $yValues
     * @param mixed $const
     * @param mixed $stats
     */
    public function testLINEST(array $expectedResult, $yValues, $xValues, $const, $stats): void
    {
        $result = Statistical\Trends::LINEST($yValues, $xValues, $const, $stats);
        self::assertIsArray($result);

        $elements = count($expectedResult);
        for ($element = 0; $element < $elements; ++$element) {
            self::assertEqualsWithDelta($expectedResult[$element], $result[$element], 1E-12);
        }
    }

    public static function providerLINEST(): array
    {
        return require 'tests/data/Calculation/Statistical/LINEST.php';
    }
}
