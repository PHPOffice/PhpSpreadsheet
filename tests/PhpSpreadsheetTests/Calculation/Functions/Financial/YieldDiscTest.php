<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class YieldDiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYIELDDISC
     *
     * @param mixed $expectedResult
     */
    public function testYIELDDISC($expectedResult, ...$args): void
    {
        $this->runTestCase('YIELDDISC', $expectedResult, $args);
    }

    public static function providerYIELDDISC(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDDISC.php';
    }
}
