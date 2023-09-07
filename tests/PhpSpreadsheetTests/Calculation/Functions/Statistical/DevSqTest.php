<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class DevSqTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDEVSQ
     */
    public function testDEVSQ(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('DEVSQ', $expectedResult, ...$args);
    }

    public static function providerDEVSQ(): array
    {
        return require 'tests/data/Calculation/Statistical/DEVSQ.php';
    }
}
