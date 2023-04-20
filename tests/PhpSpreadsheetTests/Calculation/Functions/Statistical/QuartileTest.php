<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class QuartileTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerQUARTILE
     *
     * @param mixed $expectedResult
     */
    public function testQUARTILE($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('QUARTILE', $expectedResult, ...$args);
    }

    public static function providerQUARTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/QUARTILE.php';
    }
}
