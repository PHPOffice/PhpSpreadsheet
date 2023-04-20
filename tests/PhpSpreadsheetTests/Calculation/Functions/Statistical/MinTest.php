<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMIN
     *
     * @param mixed $expectedResult
     */
    public function testMIN($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('MIN', $expectedResult, ...$args);
    }

    public static function providerMIN(): array
    {
        return require 'tests/data/Calculation/Statistical/MIN.php';
    }
}
