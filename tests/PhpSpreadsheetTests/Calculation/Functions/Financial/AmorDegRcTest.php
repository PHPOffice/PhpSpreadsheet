<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AmorDegRcTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAMORDEGRC
     *
     * @param mixed $expectedResult
     */
    public function testAMORDEGRC($expectedResult, ...$args): void
    {
        $this->runTestCase('AMORDEGRC', $expectedResult, $args);
    }

    public static function providerAMORDEGRC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORDEGRC.php';
    }
}
