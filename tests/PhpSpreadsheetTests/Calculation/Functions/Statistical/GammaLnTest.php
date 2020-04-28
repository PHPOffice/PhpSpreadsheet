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
     * @param $value
     */
    public function testGAMMALN($expectedResult, $value)
    {
        $result = Statistical::GAMMALN($value);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMALN()
    {
        return require 'data/Calculation/Statistical/GAMMALN.php';
    }
}
