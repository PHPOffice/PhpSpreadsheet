<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupNcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPNCD
     *
     * @param mixed $expectedResult
     */
    public function testCOUPNCD($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPNCD', $expectedResult, $args);
    }

    public static function providerCOUPNCD(): array
    {
        return require 'tests/data/Calculation/Financial/COUPNCD.php';
    }
}
