<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class PercentRankTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPERCENTRANK
     *
     * @param mixed $expectedResult
     * @param mixed[] $valueSet
     * @param mixed $value
     * @param mixed $digits
     */
    public function testPERCENTRANK($expectedResult, $valueSet, $value, $digits = 3): void
    {
        $result = Statistical::PERCENTRANK($valueSet, $value, $digits);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerPERCENTRANK(): array
    {
        return require 'tests/data/Calculation/Statistical/PERCENTRANK.php';
    }
}
