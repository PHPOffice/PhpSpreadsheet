<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class QuartileTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerQUARTILE
     */
    public function testQUARTILE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('QUARTILE', $expectedResult, ...$args);
    }

    public static function providerQUARTILE(): array
    {
        return require 'tests/data/Calculation/Statistical/QUARTILE.php';
    }
}
