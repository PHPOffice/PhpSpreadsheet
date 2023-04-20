<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class ReceivedTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRECEIVED
     *
     * @param mixed $expectedResult
     */
    public function testRECEIVED($expectedResult, ...$args): void
    {
        $this->runTestCase('RECEIVED', $expectedResult, $args);
    }

    public static function providerRECEIVED(): array
    {
        return require 'tests/data/Calculation/Financial/RECEIVED.php';
    }
}
