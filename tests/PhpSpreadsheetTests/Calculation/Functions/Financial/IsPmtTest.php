<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IsPmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerISPMT
     *
     * @param mixed $expectedResult
     */
    public function testISPMT($expectedResult, ...$args): void
    {
        $this->runTestCase('ISPMT', $expectedResult, $args);
    }

    public static function providerISPMT(): array
    {
        return require 'tests/data/Calculation/Financial/ISPMT.php';
    }
}
