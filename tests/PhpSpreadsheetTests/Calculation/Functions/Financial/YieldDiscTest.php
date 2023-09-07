<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class YieldDiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYIELDDISC
     */
    public function testYIELDDISC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('YIELDDISC', $expectedResult, $args);
    }

    public static function providerYIELDDISC(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDDISC.php';
    }
}
