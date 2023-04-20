<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINA
     *
     * @param mixed $expectedResult
     */
    public function testMINA($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('MINA', $expectedResult, ...$args);
    }

    public static function providerMINA(): array
    {
        return require 'tests/data/Calculation/Statistical/MINA.php';
    }
}
