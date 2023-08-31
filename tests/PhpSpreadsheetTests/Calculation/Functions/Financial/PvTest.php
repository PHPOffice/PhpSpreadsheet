<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPV
     *
     * @param mixed $expectedResult
     */
    public function testPV($expectedResult, array $args): void
    {
        $this->runTestCase('PV', $expectedResult, $args);
    }

    public static function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}
