<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GeoMeanTest extends TestCase
{
    /**
     * @dataProvider providerGEOMEAN
     *
     * @param mixed $expectedResult
     */
    public function testGEOMEAN($expectedResult, ...$args): void
    {
        $result = Statistical\Averages\Mean::geometric(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGEOMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/GEOMEAN.php';
    }
}
