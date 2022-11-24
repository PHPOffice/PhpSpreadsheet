<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO Run in Spreadsheet context.
class AverageIfTest extends TestCase
{
    /**
     * @dataProvider providerAVERAGEIF
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIF($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::AVERAGEIF(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGEIF(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEIF.php';
    }
}
