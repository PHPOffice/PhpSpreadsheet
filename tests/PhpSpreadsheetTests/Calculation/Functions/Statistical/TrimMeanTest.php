<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class TrimMeanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTRIMMEAN
     */
    public function testTRIMMEAN(mixed $expectedResult, array $args, mixed $percentage): void
    {
        $this->runTestCaseReference('TRIMMEAN', $expectedResult, $args, $percentage);
    }

    public static function providerTRIMMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/TRIMMEAN.php';
    }
}
