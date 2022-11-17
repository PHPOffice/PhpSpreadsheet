<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountATest extends TestCase
{
    /**
     * @dataProvider providerCOUNTA
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTA($expectedResult, ...$args): void
    {
        $result = Statistical\Counts::COUNTA(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTA(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTA.php';
    }
}
