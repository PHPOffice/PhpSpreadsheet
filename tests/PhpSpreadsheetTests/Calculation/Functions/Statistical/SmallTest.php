<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SmallTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSMALL
     *
     * @param mixed $expectedResult
     * @param mixed $values
     * @param mixed $position
     */
    public function testSMALL($expectedResult, $values, $position): void
    {
        $this->runTestCaseReference('SMALL', $expectedResult, $values, $position);
    }

    public static function providerSMALL(): array
    {
        return require 'tests/data/Calculation/Statistical/SMALL.php';
    }
}
