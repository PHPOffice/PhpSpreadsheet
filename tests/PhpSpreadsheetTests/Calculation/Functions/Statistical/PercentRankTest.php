<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class PercentRankTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPERCENTRANK
     *
     * @param mixed $expectedResult
     * @param mixed[] $valueSet
     * @param mixed $value
     * @param mixed $digits
     */
    public function testPERCENTRANK($expectedResult, $valueSet, $value, $digits = null): void
    {
        if ($digits === null) {
            $this->runTestCaseReference('PERCENTRANK', $expectedResult, $valueSet, $value);
        } else {
            $this->runTestCaseReference('PERCENTRANK', $expectedResult, $valueSet, $value, $digits);
        }
    }

    public static function providerPERCENTRANK(): array
    {
        return require 'tests/data/Calculation/Statistical/PERCENTRANK.php';
    }
}
