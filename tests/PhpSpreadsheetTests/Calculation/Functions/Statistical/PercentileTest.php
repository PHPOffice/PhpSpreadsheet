<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class PercentileTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPERCENTILE
     */
    public function testPERCENTILE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('PERCENTILE', $expectedResult, ...$args);
    }

    public static function providerPERCENTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/PERCENTILE.php';
    }
}
