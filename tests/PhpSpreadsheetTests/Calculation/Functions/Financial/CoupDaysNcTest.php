<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDaysNcTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYSNC
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYSNC($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPDAYSNC', $expectedResult, $args);
    }

    public static function providerCOUPDAYSNC(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYSNC.php';
    }
}
