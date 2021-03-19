<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarPTest extends TestCase
{
    /**
     * @dataProvider providerVARP
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARP($expectedResult, $values): void
    {
        $result = Statistical::VARP($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARP()
    {
        return require 'tests/data/Calculation/Statistical/VARP.php';
    }
}
