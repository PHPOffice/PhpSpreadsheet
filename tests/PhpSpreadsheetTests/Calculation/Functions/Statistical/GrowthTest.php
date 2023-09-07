<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run test in spreadsheet context
class GrowthTest extends TestCase
{
    /**
     * @dataProvider providerGROWTH
     */
    public function testGROWTH(mixed $expectedResult, array $yValues, array $xValues, ?array $newValues = null, ?bool $const = null): void
    {
        if ($newValues === null) {
            $result = Statistical\Trends::GROWTH($yValues, $xValues);
        } elseif ($const === null) {
            $result = Statistical\Trends::GROWTH($yValues, $xValues, $newValues);
        } else {
            $result = Statistical\Trends::GROWTH($yValues, $xValues, $newValues, $const);
        }

        self::assertEqualsWithDelta($expectedResult, $result[0], 1E-12);
    }

    public static function providerGROWTH(): array
    {
        return require 'tests/data/Calculation/Statistical/GROWTH.php';
    }
}
