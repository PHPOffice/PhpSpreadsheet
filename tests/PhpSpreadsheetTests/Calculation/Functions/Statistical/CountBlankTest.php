<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;
use PHPUnit\Framework\TestCase;

class CountBlankTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOUNTBLANK
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTBLANK($expectedResult, ...$args): void
    {
        $result = Counts::COUNTBLANK(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCOUNTBLANK(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTBLANK.php';
    }
}
