<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPV
     */
    public function testPV(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PV', $expectedResult, $args);
    }

    public static function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}
