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
        $this->runTestCases('MINA', $expectedResult, ...$args);
    }

    public function providerMINA(): array
    {
        return require 'tests/data/Calculation/Statistical/MINA.php';
    }
}
