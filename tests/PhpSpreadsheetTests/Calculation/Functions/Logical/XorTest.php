<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class XorTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerXOR
     *
     * @param mixed $expectedResult
     */
    public function testXOR($expectedResult, ...$args): void
    {
        $this->runTestCase('XOR', $expectedResult, ...$args);
    }

    public function providerXOR(): array
    {
        return require 'tests/data/Calculation/Logical/XOR.php';
    }
}
