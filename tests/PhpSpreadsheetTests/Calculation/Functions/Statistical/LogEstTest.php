<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO run test in spreadsheet context
class LogEstTest extends TestCase
{
    /**
     * @dataProvider providerLOGEST
     */
    public function testLOGEST(array $expectedResult, array $yValues, array $xValues, mixed $const, mixed $stats): void
    {
        $result = Statistical\Trends::LOGEST($yValues, $xValues, $const, $stats);
        self::assertIsArray($result);

        $elements = count($expectedResult);
        for ($element = 0; $element < $elements; ++$element) {
            self::assertEqualsWithDelta($expectedResult[$element], $result[$element], 1E-12);
        }
    }

    public static function providerLOGEST(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGEST.php';
    }
}
