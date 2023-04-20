<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NPerTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNPER
     *
     * @param mixed $expectedResult
     */
    public function testNPER($expectedResult, array $args): void
    {
        $this->runTestCase('NPER', $expectedResult, $args);
    }

    public static function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}
