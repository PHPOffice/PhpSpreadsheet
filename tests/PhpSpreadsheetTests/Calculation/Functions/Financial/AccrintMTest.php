<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AccrintMTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACCRINTM
     *
     * @param mixed $expectedResult
     */
    public function testACCRINTM($expectedResult, ...$args): void
    {
        $this->runTestCase('ACCRINTM', $expectedResult, $args);
    }

    public static function providerACCRINTM(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINTM.php';
    }
}
