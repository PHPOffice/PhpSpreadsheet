<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function tearDown()
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale('en_us');
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
        self::assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
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
        self::assertInternalType('callable', $functionCall);
    }

    public function providerGetFunctions()
    {
        return Calculation::getInstance()->getFunctions();
    }

    public function testIsImplemented()
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->isImplemented('non-existing-function'));
        self::assertFalse($calculation->isImplemented('AREAS'));
        self::assertTrue($calculation->isImplemented('coUNt'));
        self::assertTrue($calculation->isImplemented('abs'));
    }

    /**
     * @dataProvider providerCanLoadAllSupportedLocales
     *
     * @param string $locale
     */
    public function testCanLoadAllSupportedLocales($locale)
    {
        $calculation = Calculation::getInstance();
        self::assertTrue($calculation->setLocale($locale));
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

    public function testDoesHandleXlfnFunctions()
    {
        $calculation = Calculation::getInstance();

        $tree = $calculation->parseFormula('=_xlfn.ISFORMULA(A1)');
        self::assertCount(3, $tree);
        $function = $tree[2];
        self::assertEquals('Function', $function['type']);

        $tree = $calculation->parseFormula('=_xlfn.STDEV.S(A1:B2)');
        self::assertCount(5, $tree);
        $function = $tree[4];
        self::assertEquals('Function', $function['type']);
    }

    public function testFormulaWithOptionalArgumentsAndRequiredCellReferenceShouldPassNullForMissingArguments()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );

        $cell = $sheet->getCell('E5');
        $cell->setValue('=OFFSET(D3, -1, -2, 1, 1)');
        self::assertEquals(5, $cell->getCalculatedValue(), 'with all arguments');

        $cell = $sheet->getCell('F6');
        $cell->setValue('=OFFSET(D3, -1, -2)');
        self::assertEquals(5, $cell->getCalculatedValue(), 'missing arguments should be filled with null');
    }

    public function testCellSetAsQuotedText()
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $cell = $workSheet->getCell('A1');

        $cell->setValue("=cmd|'/C calc'!A0");
        $cell->getStyle()->setQuotePrefix(true);

        self::assertEquals("=cmd|'/C calc'!A0", $cell->getCalculatedValue());
    }

    public function testCellWithDdeExpresion()
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $cell = $workSheet->getCell('A1');

        $cell->setValue("=cmd|'/C calc'!A0");

        self::assertEquals("=cmd|'/C calc'!A0", $cell->getCalculatedValue());
    }
}
