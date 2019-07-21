<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BetaInvTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBETAINV
     *
     * @param mixed $expectedResult
     */
    public function testBETAINV($expectedResult, ...$args)
    {
        $result = Statistical::BETAINV(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBETAINV()
    {
        return require 'data/Calculation/Statistical/BETAINV.php';
    }
}
