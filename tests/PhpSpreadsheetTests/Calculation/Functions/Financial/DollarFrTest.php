<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DollarFrTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDOLLARFR
     */
    public function testDOLLARFR(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('DOLLARFR', $expectedResult, $args);
    }

    public static function providerDOLLARFR(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARFR.php';
    }
}
