<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BetaInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBETAINV
     *
     * @param mixed $expectedResult
     */
    public function testBETAINV($expectedResult, ...$args): void
    {
        $result = Statistical::BETAINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBETAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/BETAINV.php';
    }
}
