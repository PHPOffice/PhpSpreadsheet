<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class GammaInvTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGAMMAINV
     *
     * @param mixed $expectedResult
     */
    public function testGAMMAINV($expectedResult, ...$args)
    {
        $result = Statistical::GAMMAINV(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerGAMMAINV()
    {
        return require 'data/Calculation/Statistical/GAMMAINV.php';
    }
}
