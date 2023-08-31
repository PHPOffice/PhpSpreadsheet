<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class IfTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIF
     *
     * @param mixed $expectedResult
     */
    public function testIF($expectedResult, ...$args): void
    {
        $this->runTestCase('IF', $expectedResult, ...$args);
    }

    public static function providerIF(): array
    {
        return require 'tests/data/Calculation/Logical/IF.php';
    }
}
