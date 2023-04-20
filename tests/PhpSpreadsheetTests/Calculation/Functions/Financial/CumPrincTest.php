<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CumPrincTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCUMPRINC
     *
     * @param mixed $expectedResult
     */
    public function testCUMPRINC($expectedResult, ...$args): void
    {
        $this->runTestCase('CUMPRINC', $expectedResult, $args);
    }

    public static function providerCUMPRINC(): array
    {
        return require 'tests/data/Calculation/Financial/CUMPRINC.php';
    }
}
