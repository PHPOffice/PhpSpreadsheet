<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class AverageATest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAVERAGEA
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEA($expectedResult, ...$args): void
    {
        $result = Statistical::AVERAGEA(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGEA(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEA.php';
    }
}
