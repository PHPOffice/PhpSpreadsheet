<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MaxTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMAX
     */
    public function testMAX(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('MAX', $expectedResult, ...$args);
    }

    public static function providerMAX(): array
    {
        return require 'tests/data/Calculation/Statistical/MAX.php';
    }
}
