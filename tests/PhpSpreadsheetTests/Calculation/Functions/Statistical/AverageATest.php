<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AverageATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVERAGEA
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEA($expectedResult, ...$args): void
    {
        $this->runTestCases('AVERAGEA', $expectedResult, ...$args);
    }

    public static function providerAVERAGEA(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEA.php';
    }
}
