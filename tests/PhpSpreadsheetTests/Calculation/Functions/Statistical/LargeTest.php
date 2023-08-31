<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class LargeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLARGE
     *
     * @param mixed $expectedResult
     */
    public function testLARGE($expectedResult, ...$args): void
    {
        $this->runTestCaseReference('LARGE', $expectedResult, ...$args);
    }

    public static function providerLARGE(): array
    {
        return require 'tests/data/Calculation/Statistical/LARGE.php';
    }
}
