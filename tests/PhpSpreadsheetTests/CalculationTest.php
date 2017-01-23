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
     * @param string $category
     * @param array|string $functionCall
     * @param string $argumentCount
     */
    public function testGetFunctions($category, $functionCall, $argumentCount)
    {
        $this->assertInternalType('callable', $functionCall);
    }

    public function providerGetFunctions()
    {
        return Calculation::getInstance()->getFunctions();
    }

    public function testIsImplemented()
    {
        $calculation = Calculation::getInstance();
        $this->assertFalse($calculation->isImplemented('non-existing-function'));
        $this->assertFalse($calculation->isImplemented('AREAS'));
        $this->assertTrue($calculation->isImplemented('coUNt'));
        $this->assertTrue($calculation->isImplemented('abs'));
    }

    /**
     * @dataProvider providerCanLoadAllSupportedLocales
     *
     * @param string $locale
     */
    public function testCanLoadAllSupportedLocales($locale)
    {
        $calculation = Calculation::getInstance();
        $this->assertTrue($calculation->setLocale($locale));
    }

    public function providerCanLoadAllSupportedLocales()
    {
        return [
            ['bg'],
            ['cs'],
            ['da'],
            ['de'],
            ['en_us'],
            ['es'],
            ['fi'],
            ['fr'],
            ['hu'],
            ['it'],
            ['nl'],
            ['no'],
            ['pl'],
            ['pt'],
            ['pt_br'],
            ['ru'],
            ['sv'],
            ['tr'],
        ];
    }
}
