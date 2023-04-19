<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IntRateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINTRATE
     *
     * @param mixed $expectedResult
     */
    public function testINTRATE($expectedResult, ...$args): void
    {
        $this->runTestCase('INTRATE', $expectedResult, $args);
    }

    public static function providerINTRATE(): array
    {
        return require 'tests/data/Calculation/Financial/INTRATE.php';
    }
}
