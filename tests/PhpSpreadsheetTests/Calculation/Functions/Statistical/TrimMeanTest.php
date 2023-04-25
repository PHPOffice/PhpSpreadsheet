<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class TrimMeanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRIMMEAN
     *
     * @param mixed $expectedResult
     * @param mixed $percentage
     */
    public function testTRIMMEAN($expectedResult, array $args, $percentage): void
    {
        $this->runTestCaseReference('TRIMMEAN', $expectedResult, $args, $percentage);
    }

    public static function providerTRIMMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/TRIMMEAN.php';
    }
}
