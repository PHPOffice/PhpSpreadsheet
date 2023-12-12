<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class SmallTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSMALL
     */
    public function testSMALL(mixed $expectedResult, mixed $values, mixed $position): void
    {
        $this->runTestCaseReference('SMALL', $expectedResult, $values, $position);
    }

    public static function providerSMALL(): array
    {
        return require 'tests/data/Calculation/Statistical/SMALL.php';
    }
}
