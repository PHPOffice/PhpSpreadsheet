<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class SmallTest extends TestCase
{
    /**
     * @dataProvider providerSMALL
     *
     * @param mixed $expectedResult
     * @param mixed $values
     * @param mixed $position
     */
    public function testSMALL($expectedResult, $values, $position): void
    {
        $result = Statistical::SMALL($values, $position);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSMALL(): array
    {
        return require 'tests/data/Calculation/Statistical/SMALL.php';
    }
}
