<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NominalTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNOMINAL
     *
     * @param mixed $expectedResult
     */
    public function testNOMINAL($expectedResult, ...$args): void
    {
        $this->runTestCase('NOMINAL', $expectedResult, $args);
    }

    public static function providerNOMINAL(): array
    {
        return require 'tests/data/Calculation/Financial/NOMINAL.php';
    }
}
