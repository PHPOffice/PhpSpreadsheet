<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages\Mean;
use PHPUnit\Framework\TestCase;

class HarMeanTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerHARMEAN
     *
     * @param mixed $expectedResult
     */
    public function testHARMEAN($expectedResult, ...$args): void
    {
        $result = Mean::harmonic(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerHARMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/HARMEAN.php';
    }
}
