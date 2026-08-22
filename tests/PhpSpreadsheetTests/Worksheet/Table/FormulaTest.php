<?php

declare(strict_types=1);

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

    public function testCellFormulaUpdateOnHeadingColumnChange(): void
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

        $worksheet->getCell('D1')->setValue('Commission %age');
        $worksheet->getCell('E1')->setValue('Commission');

        // Verify modified formulae
        // Row Formula
        self::assertSame("=DeptSales[[#This Row],[Sales\u{a0}Amount]]*DeptSales[[#This Row],[Commission %age]]", $worksheet->getCell('E2')->getValue());
        // Totals Formula
        self::assertSame('=SUBTOTAL(109,DeptSales[Commission])', $worksheet->getCell('E8')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCellFormulaUpdateOnHeadingColumnChangeSlash(): void
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

        $worksheet->getCell('D1')->setValue('Commission %age');
        $worksheet->getCell('E1')->setValue('Commission/Amount');
        $worksheet->getCell('E1')->setValue('Commission/Amount2');

        // Verify modified formulae
        // Row Formula
        self::assertSame("=DeptSales[[#This Row],[Sales\u{a0}Amount]]*DeptSales[[#This Row],[Commission %age]]", $worksheet->getCell('E2')->getValue());
        // Totals Formula
        self::assertSame('=SUBTOTAL(109,DeptSales[Commission/Amount2])', $worksheet->getCell('E8')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testNamedFormulaUpdateOnHeadingColumnChange(): void
    {
        $reader = new Xlsx();
        $filename = 'tests/data/Worksheet/Table/TableFormulae.xlsx';
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

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

        $worksheet->getCell('E1')->setValue('Commission');
        // Verify modified formula
        self::assertSame('SUBTOTAL(109,DeptSales[Commission])', $namedFormula->getFormula());

        $spreadsheet->disconnectWorksheets();
    }
}
