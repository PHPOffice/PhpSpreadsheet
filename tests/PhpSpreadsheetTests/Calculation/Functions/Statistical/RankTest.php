<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class RankTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRANK
     *
     * @param mixed $expectedResult
     * @param mixed $value
     * @param mixed[] $valueSet
     * @param mixed $order
     */
    public function testRANK($expectedResult, $value, $valueSet, $order = null): void
    {
        if ($order === null) {
            $this->runTestCaseReference('RANK', $expectedResult, $value, $valueSet);
        } else {
            $this->runTestCaseReference('RANK', $expectedResult, $value, $valueSet, $order);
        }
    }

    public static function providerRANK(): array
    {
        return require 'tests/data/Calculation/Statistical/RANK.php';
    }
}
