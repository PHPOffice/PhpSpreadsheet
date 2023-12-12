<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AveDevTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVEDEV
     */
    public function testAVEDEV(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('AVEDEV', $expectedResult, ...$args);
    }

    public static function providerAVEDEV(): array
    {
        return require 'tests/data/Calculation/Statistical/AVEDEV.php';
    }
}
