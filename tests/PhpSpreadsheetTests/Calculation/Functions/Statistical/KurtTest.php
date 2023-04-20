<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class KurtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerKURT
     *
     * @param mixed $expectedResult
     */
    public function testKURT($expectedResult, ...$args): void
    {
        $this->runTestCases('KURT', $expectedResult, ...$args);
    }

    public static function providerKURT(): array
    {
        return require 'tests/data/Calculation/Statistical/KURT.php';
    }
}
