<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class AverageTest extends TestCase
{
    /**
     * @dataProvider providerAVERAGE
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGE($expectedResult, ...$args): void
    {
        $result = Statistical\Averages::average(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGE(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGE.php';
    }
}
