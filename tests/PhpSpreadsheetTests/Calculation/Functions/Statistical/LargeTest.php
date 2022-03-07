<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class LargeTest extends TestCase
{
    /**
     * @dataProvider providerLARGE
     *
     * @param mixed $expectedResult
     * @param mixed $values
     * @param mixed $position
     */
    public function testLARGE($expectedResult, $values, $position): void
    {
        $result = Statistical::LARGE($values, $position);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerLARGE(): array
    {
        return require 'tests/data/Calculation/Statistical/LARGE.php';
    }
}
