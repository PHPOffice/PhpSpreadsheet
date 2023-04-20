<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDaysTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYS($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPDAYS', $expectedResult, $args);
    }

    public static function providerCOUPDAYS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYS.php';
    }
}
