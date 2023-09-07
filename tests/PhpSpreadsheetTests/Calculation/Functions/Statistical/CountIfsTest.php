<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

// TODO There are some commented out cases which don't return correct value
class CountIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTIFS
     */
    public function testCOUNTIFS(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('COUNTIFS', $expectedResult, ...$args);
    }

    public static function providerCOUNTIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIFS.php';
    }
}
