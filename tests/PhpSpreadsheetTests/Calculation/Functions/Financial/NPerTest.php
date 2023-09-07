<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NPerTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNPER
     */
    public function testNPER(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('NPER', $expectedResult, $args);
    }

    public static function providerNPER(): array
    {
        return require 'tests/data/Calculation/Financial/NPER.php';
    }
}
