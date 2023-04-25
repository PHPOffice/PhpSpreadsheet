<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CoupDayBsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUPDAYBS
     *
     * @param mixed $expectedResult
     */
    public function testCOUPDAYBS($expectedResult, ...$args): void
    {
        $this->runTestCase('COUPDAYBS', $expectedResult, $args);
    }

    public static function providerCOUPDAYBS(): array
    {
        return require 'tests/data/Calculation/Financial/COUPDAYBS.php';
    }
}
