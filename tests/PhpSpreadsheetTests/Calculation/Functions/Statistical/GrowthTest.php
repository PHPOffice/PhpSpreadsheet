<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GrowthTest extends TestCase
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
    public function testGROWTH($expectedResult, array $yValues, array $xValues, ?array $newValues = null, ?bool $const = null): void
    {
        if ($newValues === null) {
            $result = Statistical::GROWTH($yValues, $xValues);
        } elseif ($const === null) {
            $result = Statistical::GROWTH($yValues, $xValues, $newValues);
        } else {
            $result = Statistical::GROWTH($yValues, $xValues, $newValues, $const);
        }

        self::assertEqualsWithDelta($expectedResult, $result[0], 1E-12);
    }

    public function providerGROWTH(): array
    {
        return require 'tests/data/Calculation/Statistical/GROWTH.php';
    }
}
