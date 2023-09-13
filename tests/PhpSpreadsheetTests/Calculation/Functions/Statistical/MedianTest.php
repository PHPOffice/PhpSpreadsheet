<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class MedianTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMEDIAN
     */
    public function testMEDIAN(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('MEDIAN', $expectedResult, ...$args);
    }

    public static function providerMEDIAN(): array
    {
        return require 'tests/data/Calculation/Statistical/MEDIAN.php';
    }
}
