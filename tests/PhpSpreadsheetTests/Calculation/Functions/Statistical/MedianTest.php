<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MedianTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMEDIAN
     *
     * @param mixed $expectedResult
     */
    public function testMEDIAN($expectedResult, ...$args): void
    {
        $this->runTestCases('MEDIAN', $expectedResult, ...$args);
    }

    public static function providerMEDIAN(): array
    {
        return require 'tests/data/Calculation/Statistical/MEDIAN.php';
    }
}
