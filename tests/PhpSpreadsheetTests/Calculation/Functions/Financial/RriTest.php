<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class RriTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRRI
     *
     * @param mixed $expectedResult
     */
    public function testRRI($expectedResult, array $args): void
    {
        $this->runTestCase('RRI', $expectedResult, $args);
    }

    public static function providerRRI(): array
    {
        return require 'tests/data/Calculation/Financial/RRI.php';
    }
}
