<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class FormulaAsStringTest extends TestCase
{
    /**
     * @dataProvider providerFunctionsAsString
     *
     * @param mixed $expectedResult
     * @param string $formula
     */
    public function testFunctionsAsString($expectedResult, $formula): void
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $workSheet->setCellValue('A1', 10);
        $workSheet->setCellValue('A2', 20);
        $workSheet->setCellValue('A3', 30);
        $workSheet->setCellValue('A4', 40);
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('namedCell', $workSheet, '$A$4'));
        $workSheet->setCellValue('B1', 'uPPER');
        $workSheet->setCellValue('B2', '=TRUE()');
        $workSheet->setCellValue('B3', '=FALSE()');

        $ws2 = $spreadsheet->createSheet();
        $ws2->setCellValue('A1', 100);
        $ws2->setCellValue('A2', 200);
        $ws2->setTitle('Sheet2');
        $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('A2B', $ws2, '$A$2'));

        $spreadsheet->setActiveSheetIndex(0);
        $cell2 = $workSheet->getCell('D1');
        $cell2->setValue($formula);
        $result = $cell2->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerFunctionsAsString(): array
    {
        return require 'tests/data/Calculation/FunctionsAsString.php';
    }
}
