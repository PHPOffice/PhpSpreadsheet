<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

class ImSumTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMSUM
     *
     * @param mixed $expectedResult
     */
    public function testIMSUM($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMSUM', $expectedResult, ...$args);
    }

    public function providerIMSUM(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSUM.php';
    }
}
