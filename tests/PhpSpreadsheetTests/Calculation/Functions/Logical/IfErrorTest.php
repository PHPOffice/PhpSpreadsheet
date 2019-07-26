<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PHPUnit\Framework\TestCase;

class IfErrorTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIFERROR
     *
     * @param mixed $expectedResult
     * @param $value
     * @param $return
     */
    public function testIFERROR($expectedResult, $value, $return)
    {
        $result = Logical::IFERROR($value, $return);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIFERROR()
    {
        return require 'data/Calculation/Logical/IFERROR.php';
    }
}
