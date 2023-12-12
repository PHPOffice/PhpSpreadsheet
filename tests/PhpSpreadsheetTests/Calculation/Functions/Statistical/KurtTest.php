<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class KurtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerKURT
     */
    public function testKURT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('KURT', $expectedResult, ...$args);
    }

    public static function providerKURT(): array
    {
        return require 'tests/data/Calculation/Statistical/KURT.php';
    }
}
