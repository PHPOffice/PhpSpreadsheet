<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class HarMeanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerHARMEAN
     *
     * @param mixed $expectedResult
     */
    public function testHARMEAN($expectedResult, ...$args): void
    {
        $this->runTestCases('HARMEAN', $expectedResult, ...$args);
    }

    public static function providerHARMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/HARMEAN.php';
    }
}
