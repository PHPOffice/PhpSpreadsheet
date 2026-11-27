<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\Expression;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Formula;
use PHPUnit\Framework\TestCase;

/**
 * LOG10 is a function, but it is also a valid cell address (column LOG, row 10).
 */
class FunctionNameLikeCellReferenceTest extends TestCase
{
    public function testInsertRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 100);
        $sheet->setCellValue('B1', '=LOG10(A1)');
        $sheet->insertNewRowBefore(1, 2);
        self::assertSame('=LOG10(A3)', $sheet->getCell('B3')->getValue());
        self::assertSame(2.0, $sheet->getCell('B3')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testInsertColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 100);
        $sheet->setCellValue('B1', '=LOG10(A1)');
        $sheet->insertNewColumnBefore('A', 2);
        self::assertSame('=LOG10(C1)', $sheet->getCell('D1')->getValue());
        self::assertSame(2.0, $sheet->getCell('D1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRemoveRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A3', 100);
        $sheet->setCellValue('B3', '=LOG10(A3)');
        $sheet->removeRow(1, 2);
        self::assertSame('=LOG10(A1)', $sheet->getCell('B1')->getValue());
        self::assertSame(2.0, $sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNamedFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->addDefinedName(new NamedFormula('LOGARITHM', $sheet, '=LOG10($A1)*2'));
        $sheet->setCellValue('A2', 100);
        $sheet->setCellValue('B2', '=LOGARITHM');
        self::assertSame(4.0, $sheet->getCell('B2')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testOdsWriterFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $formula = new Formula($spreadsheet->getDefinedNames());
        self::assertSame("of:=LOG10(['S'.A1])", $formula->convertFormula('=LOG10(A1)', 'S'));
        $spreadsheet->disconnectWorksheets();
    }

    public function testConditionalFormattingWizard(): void
    {
        $wizard = new Expression('C3');
        $wizard->expression('LOG10($A3)>1');
        self::assertSame(['LOG10($A5)>1'], $wizard->getConditional()->getConditions());
    }
}
