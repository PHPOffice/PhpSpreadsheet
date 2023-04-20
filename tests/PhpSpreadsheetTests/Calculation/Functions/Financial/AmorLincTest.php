<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AmorLincTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAMORLINC
     *
     * @param mixed $expectedResult
     */
    public function testAMORLINC($expectedResult, ...$args): void
    {
        $this->runTestCase('AMORLINC', $expectedResult, $args);
    }

    public static function providerAMORLINC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }
}
