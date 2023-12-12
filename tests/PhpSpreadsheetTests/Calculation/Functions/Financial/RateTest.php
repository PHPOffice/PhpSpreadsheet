<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class RateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRATE
     */
    public function testRATE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('RATE', $expectedResult, $args);
    }

    public static function providerRATE(): array
    {
        return require 'tests/data/Calculation/Financial/RATE.php';
    }
}
