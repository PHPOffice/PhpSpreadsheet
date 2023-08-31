<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NpvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNPV
     *
     * @param mixed $expectedResult
     */
    public function testNPV($expectedResult, ...$args): void
    {
        $this->runTestCase('NPV', $expectedResult, $args);
    }

    public static function providerNPV(): array
    {
        return require 'tests/data/Calculation/Financial/NPV.php';
    }
}
