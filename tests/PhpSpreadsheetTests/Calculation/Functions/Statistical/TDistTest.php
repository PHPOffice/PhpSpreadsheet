<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TDistTest extends TestCase
{
    /**
     * @dataProvider providerTDIST
     *
     * @param mixed $expectedResult
     * @param mixed $degrees
     * @param mixed $value
     * @param mixed $tails
     */
    public function testTDIST($expectedResult, $value, $degrees, $tails): void
    {
        $result = Statistical::TDIST($value, $degrees, $tails);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/TDIST.php';
    }
}
