<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class SlnTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSLN
     *
     * @param mixed $expectedResult
     */
    public function testSLN($expectedResult, array $args): void
    {
        $this->runTestCase('SLN', $expectedResult, $args);
    }

    public static function providerSLN(): array
    {
        return require 'tests/data/Calculation/Financial/SLN.php';
    }
}
