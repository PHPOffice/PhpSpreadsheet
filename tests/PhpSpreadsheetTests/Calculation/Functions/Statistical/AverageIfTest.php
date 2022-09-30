<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;
use PHPUnit\Framework\TestCase;

class AverageIfTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAVERAGEIF
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIF($expectedResult, ...$args): void
    {
        $result = Conditional::averageIf(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGEIF(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEIF.php';
    }
}
