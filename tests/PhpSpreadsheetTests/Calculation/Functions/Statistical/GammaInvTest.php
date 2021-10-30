<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaInvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMAINV
     *
     * @param mixed $expectedResult
     */
    public function testGAMMAINV($expectedResult, ...$args): void
    {
        $result = Statistical::GAMMAINV(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMAINV(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMAINV.php';
    }
}
