<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMIN
     */
    public function testMIN(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('MIN', $expectedResult, ...$args);
    }

    public static function providerMIN(): array
    {
        return require 'tests/data/Calculation/Statistical/MIN.php';
    }
}
