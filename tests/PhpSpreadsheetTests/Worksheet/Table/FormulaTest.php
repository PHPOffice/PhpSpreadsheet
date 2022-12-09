<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    public function testCellFormulaUpdateOnTableNameChange(): void
    {
        $reader = new Xlsx();
        $filename = 'tests/data/Worksheet/Table/TableFormulae.xlsx';
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        // Verify original formulae
        // Row Formula
        self::assertSame("=DeptSales[[#This Row],[Sales\u{a0}Amount]]*DeptSales[[#This Row],[% Commission]]", $worksheet->getCell('E2')->getValue());
        // Totals Formula
        self::assertSame('=SUBTOTAL(109,DeptSales[Commission Amount])', $worksheet->getCell('E8')->getValue());

        $table = $worksheet->getTableCollection()[0];
        if ($table === null) {
            self::markTestSkipped('Unable to read table for testing.');
        }
        $table->setName('tblSalesByDepartment');

        // Verify modified formulae
        // Row Formula
        self::assertSame("=tblSalesByDepartment[[#This Row],[Sales\u{a0}Amount]]*tblSalesByDepartment[[#This Row],[% Commission]]", $worksheet->getCell('E2')->getValue());
        // Totals Formula
        self::assertSame('=SUBTOTAL(109,tblSalesByDepartment[Commission Amount])', $worksheet->getCell('E8')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testNamedFormulaUpdateOnTableNameChange(): void
    {
        $reader = new Xlsx();
        $filename = 'tests/data/Worksheet/Table/TableFormulae.xlsx';
        $spreadsheet = $reader->load($filename);

        $table = $spreadsheet->getActiveSheet()->getTableCollection()[0];
        if ($table === null) {
            self::markTestSkipped('Unable to read table for testing.');
        }
        $namedFormula = $spreadsheet->getNamedFormula('CommissionTotal');
        if ($namedFormula === null) {
            self::markTestSkipped('Unable to read named formula for testing.');
        }

        // Verify original formula
        self::assertSame('SUBTOTAL(109,DeptSales[Commission Amount])', $namedFormula->getFormula());

        $table->setName('tblSalesByDepartment');
        // Verify modified formula
        self::assertSame('SUBTOTAL(109,tblSalesByDepartment[Commission Amount])', $namedFormula->getFormula());

        $spreadsheet->disconnectWorksheets();
    }
}
