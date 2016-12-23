<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class CalculationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBinaryComparisonOperation
     *
     * @param mixed $formula
     * @param mixed $expectedResultExcel
     * @param mixed $expectedResultOpenOffice
     */
    public function testBinaryComparisonOperation($formula, $expectedResultExcel, $expectedResultOpenOffice)
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $resultExcel = Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public function providerBinaryComparisonOperation()
    {
        return require 'data/CalculationBinaryComparisonOperation.php';
    }

    /**
     * @dataProvider providerGetFunctions
     *
     * @param mixed $category
     * @param mixed $functionCall
     * @param mixed $argumentCount
     */
    public function testGetFunctions($category, $functionCall, $argumentCount)
    {
        $this->assertInternalType('callable', $functionCall);
    }

    public function providerGetFunctions()
    {
        return Calculation::getInstance()->getFunctions();
    }
}
