<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AveDevTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVEDEV
     *
     * @param mixed $expectedResult
     */
    public function testAVEDEV($expectedResult, ...$args): void
    {
        $this->runTestCases('AVEDEV', $expectedResult, ...$args);
    }

    public static function providerAVEDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/AVEDEV.php';
    }
}
