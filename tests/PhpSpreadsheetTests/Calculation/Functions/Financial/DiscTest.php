<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDISC
     *
     * @param mixed $expectedResult
     */
    public function testDISC($expectedResult, ...$args): void
    {
        $this->runTestCase('DISC', $expectedResult, $args);
    }

    public static function providerDISC(): array
    {
        return require 'tests/data/Calculation/Financial/DISC.php';
    }
}
