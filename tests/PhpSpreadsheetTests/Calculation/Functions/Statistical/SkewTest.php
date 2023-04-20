<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SkewTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSKEW
     *
     * @param mixed $expectedResult
     */
    public function testSKEW($expectedResult, array $args): void
    {
        $this->runTestCaseReference('SKEW', $expectedResult, ...$args);
    }

    public static function providerSKEW(): array
    {
        return require 'tests/data/Calculation/Statistical/SKEW.php';
    }
}
