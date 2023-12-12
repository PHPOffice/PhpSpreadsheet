<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MinATest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMINA
     */
    public function testMINA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('MINA', $expectedResult, ...$args);
    }

    public static function providerMINA(): array
    {
        return require 'tests/data/Calculation/Statistical/MINA.php';
    }
}
