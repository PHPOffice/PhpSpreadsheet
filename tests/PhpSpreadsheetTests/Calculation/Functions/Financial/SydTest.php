<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class SydTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSYD
     *
     * @param mixed $expectedResult
     */
    public function testSYD($expectedResult, array $args): void
    {
        $this->runTestCase('SYD', $expectedResult, $args);
    }

    public static function providerSYD(): array
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}
