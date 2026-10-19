<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class TableTotalsRowFormulaTest extends AbstractFunctional
{
    private const EXCEL_FILE = 'tests/data/Reader/XLSX/TableWithoutFilter.xlsx';

    // The column name in this file contains a non-breaking space
    private const EXCEL_FORMULA = "SUBTOTAL(109,DeptSales[Sales\u{a0}Amount])";

    private static function disablePrecalculation(XlsxWriter $writer): void
    {
        $writer->setPreCalculateFormulas(false);
    }

    public function testReadTotalsRowFormula(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::EXCEL_FILE);
        $table = $spreadsheet->getActiveSheet()->getTableCollection()[0];
        self::assertInstanceOf(Table::class, $table);

        $column = $table->getColumn('C');
        self::assertSame('custom', $column->getTotalsRowFunction());
        self::assertSame(self::EXCEL_FORMULA, $column->getTotalsRowFormula());

        $column = $table->getColumn('E');
        self::assertSame('sum', $column->getTotalsRowFunction());
        self::assertNull($column->getTotalsRowFormula());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTotalsRowFormulaSurvivesLoadAndSave(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::EXCEL_FILE);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, self::disablePrecalculation(...));
        $spreadsheet->disconnectWorksheets();
        $table = $reloadedSpreadsheet->getActiveSheet()->getTableCollection()[0];
        self::assertInstanceOf(Table::class, $table);

        $column = $table->getColumn('C');
        self::assertSame('custom', $column->getTotalsRowFunction());
        self::assertSame(self::EXCEL_FORMULA, $column->getTotalsRowFormula());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testWriteTotalsRowFormula(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Name', 'Qty', 'Price'],
            ['a', 1, 2],
            ['b', 3, 4],
            ['Total', '=SUBTOTAL(109,Sales[Qty])*2', '=SUBTOTAL(109,Sales[Price])'],
        ]);
        $table = new Table('A1:C4', 'Sales');
        $table->setShowTotalsRow(true);
        $table->getColumn('A')->setTotalsRowLabel('Total');
        // Leading "=" is optional, Excel stores the formula without it
        $table->getColumn('B')->setTotalsRowFormula('=SUBTOTAL(109,Sales[Qty])*2');
        $table->getColumn('C')->setTotalsRowFunction('sum');
        $sheet->addTable($table);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, self::disablePrecalculation(...));
        $spreadsheet->disconnectWorksheets();
        $table = $reloadedSpreadsheet->getActiveSheet()->getTableCollection()[0];
        self::assertInstanceOf(Table::class, $table);

        $column = $table->getColumn('B');
        self::assertSame('custom', $column->getTotalsRowFunction());
        self::assertSame('SUBTOTAL(109,Sales[Qty])*2', $column->getTotalsRowFormula());

        $column = $table->getColumn('C');
        self::assertSame('sum', $column->getTotalsRowFunction());
        self::assertNull($column->getTotalsRowFormula());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testTotalsRowFormulaNotWrittenWithoutTotalsRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Name', 'Qty'],
            ['a', 1],
        ]);
        $table = new Table('A1:B2', 'Sales');
        $table->getColumn('B')->setTotalsRowFormula('SUBTOTAL(109,Sales[Qty])');
        $sheet->addTable($table);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, self::disablePrecalculation(...));
        $spreadsheet->disconnectWorksheets();
        $table = $reloadedSpreadsheet->getActiveSheet()->getTableCollection()[0];
        self::assertInstanceOf(Table::class, $table);

        $column = $table->getColumn('B');
        self::assertNull($column->getTotalsRowFunction());
        self::assertNull($column->getTotalsRowFormula());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
