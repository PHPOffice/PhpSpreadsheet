<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TinvTest extends TestCase
{
    /**
     * @dataProvider providerTINV
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testTINV($expectedResult, $probability, $degrees): void
    {
        $result = Statistical::TINV($probability, $degrees);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTINV()
    {
        return require 'tests/data/Calculation/Statistical/TINV.php';
    }
}
