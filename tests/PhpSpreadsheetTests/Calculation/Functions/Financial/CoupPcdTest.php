<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupPcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPPCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPPCD($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPPCD', $expectedResult, $args);
    }

    public static function providerCOUPPCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPPCD.php';
    }
}
