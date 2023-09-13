<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IntRateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINTRATE
     */
    public function testINTRATE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('INTRATE', $expectedResult, $args);
    }

    public static function providerINTRATE(): array
    {
        return require 'tests/data/Calculation/Financial/INTRATE.php';
    }
}
