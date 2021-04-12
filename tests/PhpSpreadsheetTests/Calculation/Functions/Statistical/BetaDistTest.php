<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BetaDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBETADIST
     *
     * @param mixed $expectedResult
     */
    public function testBETADIST($expectedResult, ...$args): void
    {
        $result = Statistical::BETADIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBETADIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BETADIST.php';
    }
}
