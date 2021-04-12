<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CorrelTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCORREL
     *
     * @param mixed $expectedResult
     * @param mixed $xargs
     * @param mixed $yargs
     */
    public function testCORREL($expectedResult, $xargs, $yargs): void
    {
        $result = Statistical::CORREL($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCORREL(): array
    {
        return require 'tests/data/Calculation/Statistical/CORREL.php';
    }
}
