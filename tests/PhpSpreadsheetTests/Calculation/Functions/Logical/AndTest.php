<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class AndTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAND
     *
     * @param mixed $expectedResult
     */
    public function testAND($expectedResult, ...$args): void
    {
        $this->runTestCase('AND', $expectedResult, ...$args);
    }

    public function providerAND(): array
    {
        return require 'tests/data/Calculation/Logical/AND.php';
    }
}
