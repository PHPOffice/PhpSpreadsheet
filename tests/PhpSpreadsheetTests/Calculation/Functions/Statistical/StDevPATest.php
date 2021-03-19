<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevPATest extends TestCase
{
    /**
     * @dataProvider providerSTDEVPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVPA($expectedResult, $values): void
    {
        $result = Statistical::STDEVPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVPA()
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA.php';
    }
}
