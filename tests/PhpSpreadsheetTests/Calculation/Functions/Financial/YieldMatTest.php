<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class YieldMatTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYIELDMAT
     *
     * @param mixed $expectedResult
     */
    public function testYIELDMAT($expectedResult, ...$args): void
    {
        $this->runTestCase('YIELDMAT', $expectedResult, $args);
    }

    public static function providerYIELDMAT(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDMAT.php';
    }
}
