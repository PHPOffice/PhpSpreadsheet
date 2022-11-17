<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class SkewTest extends TestCase
{
    /**
     * @dataProvider providerSKEW
     *
     * @param mixed $expectedResult
     */
    public function testSKEW($expectedResult, array $args): void
    {
        $result = Statistical\Deviations::skew($args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSKEW(): array
    {
        return require 'tests/data/Calculation/Statistical/SKEW.php';
    }
}
