<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class TableTest extends AbstractFunctional
{
    protected function populateData(Spreadsheet $spreadsheet): Table
    {
        $worksheet = $spreadsheet->getActiveSheet();

        $tableName = 'SalesData';
        $columnFormula = "=SUM({$tableName}[[#This Row],[Q1]:[Q4]])";

        $dataArray = [
            ['Year', 'Country', 'Q1', 'Q2', 'Q3', 'Q4', 'Sales'],
            [2010, 'Belgium', 380, 390, 420, 460, $columnFormula],
            [2010, 'France', 510, 490, 460, 590, $columnFormula],
            [2010, 'Germany', 720, 680, 640, 660, $columnFormula],
            [2010, 'Italy', 440, 410, 420, 450, $columnFormula],
            [2010, 'Spain', 510, 490, 470, 420, $columnFormula],
            [2010, 'UK', 690, 610, 620, 600, $columnFormula],
            [2010, 'United States', 790, 730, 860, 850, $columnFormula],
            [2011, 'Belgium', 400, 350, 450, 500, $columnFormula],
            [2011, 'France', 620, 650, 415, 570, $columnFormula],
            [2011, 'Germany', 680, 620, 710, 690, $columnFormula],
            [2011, 'Italy', 430, 370, 350, 335, $columnFormula],
            [2011, 'Spain', 460, 390, 430, 415, $columnFormula],
            [2011, 'UK', 720, 650, 580, 510, $columnFormula],
            [2011, 'United States', 800, 700, 900, 950, $columnFormula],
        ];

        $worksheet->fromArray($dataArray, null, 'A1');

        $rowColumnRange = "{$worksheet->getHighestDataColumn()}{$worksheet->getHighestDataRow()}";

        $table = new Table("A1:{$rowColumnRange}", $tableName);
        $table->setRange("A1:{$rowColumnRange}");

        $table->getColumn('G')
            ->setTotalsRowLabel('Total')
            ->setColumnFormula($columnFormula);
        $worksheet->getCell('A16')->setValue('Total');
        $worksheet->getCell('G16')->setValue("=SUBTOTAL(109,{$tableName}[Sales])");

        $spreadsheet->getActiveSheet()->addTable($table);

        return $table;
    }

    public function testTableCreation(): void
    {
        $spreadsheet = new Spreadsheet();

        $this->populateData($spreadsheet);

        // TODO: We don't yet support Structured References in formulae, so we need to disable precalculation
        //       when writing.
        $disablePrecalculation = function (Xlsx $writer): void {
            $writer->setPreCalculateFormulas(false);
        };

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, $disablePrecalculation);
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();

        $reloadedTable = $reloadedWorksheet->getTableCollection()[0];
        self::assertNotNull($reloadedTable);
    }

    public function testTableWithoutFilter(): void
    {
        $spreadsheet = new Spreadsheet();

        $table = $this->populateData($spreadsheet);
        $table->setAllowFilter(false);

        // TODO: We don't yet support Structured References in formulae, so we need to disable precalc when writing
        $disablePrecalculation = function (Xlsx $writer): void {
            $writer->setPreCalculateFormulas(false);
        };

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', null, $disablePrecalculation);
        $reloadedWorksheet = $reloadedSpreadsheet->getActiveSheet();

        $reloadedTable = $reloadedWorksheet->getTableCollection()[0];
        /** @var Table $reloadedTable */
        self::assertNotNull($reloadedTable);
        self::assertFalse($reloadedTable->getAllowFilter());
    }
}
