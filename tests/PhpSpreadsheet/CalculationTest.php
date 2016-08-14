<?php

namespace PHPExcel;

class CalculationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Calculation\Functions::setCompatibilityMode(Calculation\Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBinaryComparisonOperation
     */
    public function testBinaryComparisonOperation($formula, $expectedResultExcel, $expectedResultOpenOffice)
    {
        Calculation\Functions::setCompatibilityMode(Calculation\Functions::COMPATIBILITY_EXCEL);
        $resultExcel = Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Calculation\Functions::setCompatibilityMode(Calculation\Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public function providerBinaryComparisonOperation()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/CalculationBinaryComparisonOperation.data');
    }
}
