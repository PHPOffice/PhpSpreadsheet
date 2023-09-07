<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SlopeTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSLOPE
     */
    public function testSLOPE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('SLOPE', $expectedResult, ...$args);
    }

    public static function providerSLOPE(): array
    {
        return require 'tests/data/Calculation/Statistical/SLOPE.php';
    }
}
