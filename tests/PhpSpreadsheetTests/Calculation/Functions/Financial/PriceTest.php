<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PriceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRICE
     *
     * @param mixed $expectedResult
     */
    public function testPRICE($expectedResult, ...$args): void
    {
        $this->runTestCase('PRICE', $expectedResult, $args);
    }

    public static function providerPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/PRICE.php';
    }

    /**
     * @dataProvider providerPRICE3
     *
     * @param mixed $expectedResult
     */
    public function testPRICE3($expectedResult, ...$args): void
    {
        // These results (PRICE function with basis codes 2 and 3)
        // agree with published algorithm, LibreOffice, and Gnumeric.
        // They do not agree with Excel.
        $this->runTestCase('PRICE', $expectedResult, $args);
    }

    public static function providerPRICE3(): array
    {
        return require 'tests/data/Calculation/Financial/PRICE3.php';
    }
}
