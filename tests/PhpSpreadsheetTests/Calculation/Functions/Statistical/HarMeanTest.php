<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class HarMeanTest extends TestCase
{
    /**
     * @dataProvider providerHARMEAN
     *
     * @param mixed $expectedResult
     */
    public function testHARMEAN($expectedResult, ...$args): void
    {
        $result = Statistical\Averages\Mean::harmonic(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerHARMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/HARMEAN.php';
    }
}
