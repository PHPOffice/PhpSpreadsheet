<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountBlankTest extends TestCase
{
    /**
     * @dataProvider providerCOUNTBLANK
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTBLANK($expectedResult, ...$args): void
    {
        $result = Statistical\Counts::COUNTBLANK(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTBLANK(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTBLANK.php';
    }
}
