<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

// TODO figure out just what Excel is doing.
// For example, A1=75, A2=94, A3=86:
// =AVERAGEIFS(A1:A3,A1:A3,">80") gives an answer, but
// =AVERAGEIFS({75;94;86},{75;94;86},">80") does not.

class AverageIfsTest extends TestCase
{
    /**
     * @dataProvider providerAVERAGEIFS
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIFS($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::AVERAGEIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGEIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEIFS.php';
    }
}
