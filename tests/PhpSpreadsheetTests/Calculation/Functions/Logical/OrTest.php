<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class OrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerOR
     *
     * @param mixed $expectedResult
     */
    public function testOR($expectedResult, ...$args): void
    {
        $this->runTestCase('OR', $expectedResult, ...$args);
    }

    public function providerOR(): array
    {
        return require 'tests/data/Calculation/Logical/OR.php';
    }
}
