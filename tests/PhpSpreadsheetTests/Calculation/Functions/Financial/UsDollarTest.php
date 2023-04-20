<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class UsDollarTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerUSDOLLAR
     *
     * @param mixed $expectedResult
     */
    public function testUSDOLLAR($expectedResult, ...$args): void
    {
        $this->runTestCase('USDOLLAR', $expectedResult, $args);
    }

    public static function providerUSDOLLAR(): array
    {
        return require 'tests/data/Calculation/Financial/USDOLLAR.php';
    }
}
