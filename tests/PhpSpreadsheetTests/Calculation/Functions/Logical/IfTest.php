<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class IfTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerIF
     */
    public function testIF(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('IF', $expectedResult, ...$args);
    }

    public static function providerIF(): array
    {
        return require 'tests/data/Calculation/Logical/IF.php';
    }
}
