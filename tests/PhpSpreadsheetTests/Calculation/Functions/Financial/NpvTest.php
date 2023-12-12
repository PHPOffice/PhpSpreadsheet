<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class NpvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNPV
     */
    public function testNPV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('NPV', $expectedResult, $args);
    }

    public static function providerNPV(): array
    {
        return require 'tests/data/Calculation/Financial/NPV.php';
    }
}
