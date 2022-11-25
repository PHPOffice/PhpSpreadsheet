<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class RateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRATE
     *
     * @param mixed $expectedResult
     */
    public function testRATE($expectedResult, ...$args): void
    {
        $this->runTestCase('RATE', $expectedResult, $args);
    }

    public function providerRATE(): array
    {
        return require 'tests/data/Calculation/Financial/RATE.php';
    }
}
