<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDISC
     */
    public function testDISC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('DISC', $expectedResult, $args);
    }

    public static function providerDISC(): array
    {
        return require 'tests/data/Calculation/Financial/DISC.php';
    }
}
