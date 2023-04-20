<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupNumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPNUM
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNUM($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPNUM', $expectedResult, $args);
    }

    public static function providerCOUPNUM(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNUM.php';
    }
}
