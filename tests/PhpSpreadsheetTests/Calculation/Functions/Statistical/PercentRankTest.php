<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class PercentRankTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPERCENTRANK
     *
     * @param mixed[] $valueSet
     */
    public function testPERCENTRANK(mixed $expectedResult, mixed $valueSet, mixed $value, mixed $digits = null): void
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
