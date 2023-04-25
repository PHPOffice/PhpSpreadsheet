<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AverageTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVERAGE
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGE($expectedResult, ...$args): void
    {
        $this->runTestCases('AVERAGE', $expectedResult, ...$args);
    }

    public static function providerAVERAGE(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGE.php';
    }
}
