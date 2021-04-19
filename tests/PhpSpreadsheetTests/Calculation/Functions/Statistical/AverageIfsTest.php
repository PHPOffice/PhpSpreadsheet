<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class AverageIfsTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

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
