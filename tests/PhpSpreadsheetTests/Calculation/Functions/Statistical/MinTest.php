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
        $this->runTestCases('MIN', $expectedResult, ...$args);
    }

    public function providerMIN(): array
    {
        return require 'tests/data/Calculation/Statistical/MIN.php';
    }
}
