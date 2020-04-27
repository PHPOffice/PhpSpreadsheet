<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMADIST
     *
     * @param mixed $expectedResult
     */
    public function testGAMMADIST($expectedResult, ...$args)
    {
        $result = Statistical::GAMMADIST(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGAMMADIST()
    {
        return require 'data/Calculation/Statistical/GAMMADIST.php';
    }
}
