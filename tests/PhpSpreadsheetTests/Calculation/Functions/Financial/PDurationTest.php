<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PDurationTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args): void
    {
        $this->runTestCase('PDURATION', $expectedResult, $args);
    }

    public static function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
