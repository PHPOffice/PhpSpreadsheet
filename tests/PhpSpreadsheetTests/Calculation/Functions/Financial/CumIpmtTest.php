<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class CumIpmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCUMIPMT
     */
    public function testCUMIPMT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('CUMIPMT', $expectedResult, $args);
    }

    public static function providerCUMIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/CUMIPMT.php';
    }
}
