<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class MedianTest extends TestCase
{
    /**
     * @dataProvider providerMEDIAN
     *
     * @param mixed $expectedResult
     */
    public function testMEDIAN($expectedResult, ...$args): void
    {
        $result = Statistical\Averages::median(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerMEDIAN(): array
    {
        return require 'tests/data/Calculation/Statistical/MEDIAN.php';
    }
}
