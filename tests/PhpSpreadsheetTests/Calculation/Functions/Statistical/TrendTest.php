<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TrendTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGROWTH
     *
     * @param mixed $expectedResult
     */
    public function testTREND($expectedResult, array $yValues, array $xValues, ?array $newValues = null, ?bool $const = null): void
    {
        if ($newValues === null) {
            $result = Statistical::TREND($yValues, $xValues);
        } elseif ($const === null) {
            $result = Statistical::TREND($yValues, $xValues, $newValues);
        } else {
            $result = Statistical::TREND($yValues, $xValues, $newValues, $const);
        }

        self::assertEqualsWithDelta($expectedResult, $result[0], 1E-12);
    }

    public function providerGROWTH(): array
    {
        return require 'tests/data/Calculation/Statistical/TREND.php';
    }
}
