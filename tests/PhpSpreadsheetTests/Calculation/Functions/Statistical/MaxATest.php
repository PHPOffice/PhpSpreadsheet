<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMAXA
     */
    public function testMAXA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('MAXA', $expectedResult, ...$args);
    }

    public static function providerMAXA(): array
    {
        return require 'tests/data/Calculation/Statistical/MAXA.php';
    }
}
