<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class FvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFV
     *
     * @param mixed $expectedResult
     */
    public function testFV($expectedResult, array $args): void
    {
        $this->runTestCase('FV', $expectedResult, $args);
    }

    public function providerFV(): array
    {
        return require 'tests/data/Calculation/Financial/FV.php';
    }
}
