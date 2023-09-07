<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SkewTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSKEW
     */
    public function testSKEW(mixed $expectedResult, array $args): void
    {
        $this->runTestCaseReference('SKEW', $expectedResult, ...$args);
    }

    public static function providerSKEW(): array
    {
        return require 'tests/data/Calculation/Statistical/SKEW.php';
    }
}
