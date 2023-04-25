<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DbTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDB
     *
     * @param mixed $expectedResult
     */
    public function testDB($expectedResult, ...$args): void
    {
        $this->runTestCase('DB', $expectedResult, $args);
    }

    public static function providerDB(): array
    {
        return require 'tests/data/Calculation/Financial/DB.php';
    }
}
