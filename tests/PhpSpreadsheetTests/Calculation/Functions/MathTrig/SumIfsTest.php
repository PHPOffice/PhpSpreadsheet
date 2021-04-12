<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class SumIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUMIFS
     *
     * @param mixed $expectedResult
     */
    public function testSUMIFS($expectedResult, ...$args): void
    {
        $result = Statistical\Conditional::SUMIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUMIFS(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMIFS.php';
    }
}
