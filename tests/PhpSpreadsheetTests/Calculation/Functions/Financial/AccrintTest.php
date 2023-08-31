<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AccrintTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACCRINT
     *
     * @param mixed $expectedResult
     */
    public function testACCRINT($expectedResult, ...$args): void
    {
        $this->runTestCase('ACCRINT', $expectedResult, $args);
    }

    public static function providerACCRINT(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINT.php';
    }
}
