<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarPATest extends TestCase
{
    /**
     * @dataProvider providerVARPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARPA($expectedResult, $values): void
    {
        $result = Statistical::VARPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARPA()
    {
        return require 'tests/data/Calculation/Statistical/VARPA.php';
    }
}
