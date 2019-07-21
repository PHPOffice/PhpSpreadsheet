<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class AveDevTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerAVEDEV
     *
     * @param mixed $expectedResult
     */
    public function testAVEDEV($expectedResult, ...$args)
    {
        $result = Statistical::AVEDEV(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerAVEDEV()
    {
        return require 'data/Calculation/Statistical/AVEDEV.php';
    }
}
