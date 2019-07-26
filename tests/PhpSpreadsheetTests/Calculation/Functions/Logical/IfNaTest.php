<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfNaTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFNA
     *
     * @param mixed $expectedResult
     * @param $value
     * @param $return
     */
    public function testIFNA($expectedResult, $value, $return)
    {
        $result = Logical::IFNA($value, $return);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIFNA()
    {
        return require 'data/Calculation/Logical/IFNA.php';
    }
}
