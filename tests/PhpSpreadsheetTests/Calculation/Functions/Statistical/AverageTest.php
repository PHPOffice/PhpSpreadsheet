<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PHPUnit\Framework\TestCase;

class AverageTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAVERAGE
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGE($expectedResult, ...$args): void
    {
        $result = Averages::average(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerAVERAGE(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGE.php';
    }
}
