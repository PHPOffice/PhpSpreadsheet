<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FDist2Test extends TestCase
{
    /**
     * @dataProvider providerFDIST2
     *
     * @param mixed $expectedResult
     */
    public function testFDIST2($expectedResult, ...$args): void
    {
        $result = Statistical::FDIST2(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerFDIST2(): array
    {
        return require 'tests/data/Calculation/Statistical/FDIST2.php';
    }
}
