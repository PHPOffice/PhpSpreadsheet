<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class SeriesSumTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     */
    public function testSERIESSUM($expectedResult, ...$args): void
    {
        $result = MathTrig::SERIESSUM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSERIESSUM()
    {
        return require 'tests/data/Calculation/MathTrig/SERIESSUM.php';
    }
}
