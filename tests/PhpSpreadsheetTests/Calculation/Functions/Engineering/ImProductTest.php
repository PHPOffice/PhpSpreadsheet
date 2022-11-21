<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

class ImProductTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testIMPRODUCT($expectedResult, ...$args): void
    {
        $this->runComplexTestCase('IMPRODUCT', $expectedResult, ...$args);
    }

    public function providerIMPRODUCT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMPRODUCT.php';
    }
}
