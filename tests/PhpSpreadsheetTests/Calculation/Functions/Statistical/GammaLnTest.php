<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaLnTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMALN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testGAMMALN($expectedResult, $value): void
    {
        $result = Statistical::GAMMALN($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMALN(): array
    {
        return require 'tests/data/Calculation/Statistical/GAMMALN.php';
    }
}
