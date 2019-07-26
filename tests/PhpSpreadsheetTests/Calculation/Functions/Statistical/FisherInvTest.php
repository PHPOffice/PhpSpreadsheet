<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class FisherInvTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFISHERINV
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testFISHERINV($expectedResult, $value)
    {
        $result = Statistical::FISHERINV($value);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerFISHERINV()
    {
        return require 'data/Calculation/Statistical/FISHERINV.php';
    }
}
