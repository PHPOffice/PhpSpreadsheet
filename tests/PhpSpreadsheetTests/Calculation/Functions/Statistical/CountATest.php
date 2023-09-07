<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTA
     */
    public function testCOUNTA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('COUNTA', $expectedResult, ...$args);
    }

    public static function providerCOUNTA(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTA.php';
    }
}
