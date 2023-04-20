<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DdbTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDDB
     *
     * @param mixed $expectedResult
     */
    public function testDDB($expectedResult, ...$args): void
    {
        $this->runTestCase('DDB', $expectedResult, $args);
    }

    public static function providerDDB(): array
    {
        return require 'tests/data/Calculation/Financial/DDB.php';
    }
}
