<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class LargeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLARGE
     */
    public function testLARGE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('LARGE', $expectedResult, ...$args);
    }

    public static function providerLARGE(): array
    {
        return require 'tests/data/Calculation/Statistical/LARGE.php';
    }
}
