<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class LogNormDist2Test extends TestCase
{
    /**
     * @dataProvider providerLOGNORMDIST2
     *
     * @param mixed $expectedResult
     */
    public function testLOGNORMDIST2($expectedResult, ...$args): void
    {
        $result = Statistical::LOGNORMDIST2(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLOGNORMDIST2(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGNORMDIST2.php';
    }
}
