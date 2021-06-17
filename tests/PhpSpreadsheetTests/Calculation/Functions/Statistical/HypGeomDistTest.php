<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class HypGeomDistTest extends TestCase
{
    /**
     * @dataProvider providerHYPGEOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testHYPGEOMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::HYPGEOMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerHYPGEOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/HYPGEOMDIST.php';
    }
}
