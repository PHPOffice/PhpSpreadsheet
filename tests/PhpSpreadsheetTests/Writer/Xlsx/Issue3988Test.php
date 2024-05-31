<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue3988Test extends TestCase
{
    public function testIssue3988(): void
    {
        // code liberally borrowed from samples/Table/01_Table
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Year')
            ->setCellValue('B1', 'Quarter')
            ->setCellValue('C1', 'Country')
            ->setCellValue('D1', 'Sales');

        $dataArray = [
            ['2010', 'Q1', 'United States', 790],
            ['2010', 'Q2', 'United States', 730],
            ['2010', 'Q3', 'United States', 860],
            ['2010', 'Q4', 'United States', 850],
            ['2011', 'Q1', 'United States', 800],
            ['2011', 'Q2', 'United States', 700],
            ['2011', 'Q3', 'United States', 900],
            ['2011', 'Q4', 'United States', 950],
            ['2010', 'Q1', 'Belgium', 380],
            ['2010', 'Q2', 'Belgium', 390],
            ['2010', 'Q3', 'Belgium', 420],
            ['2010', 'Q4', 'Belgium', 460],
            ['2011', 'Q1', 'Belgium', 400],
            ['2011', 'Q2', 'Belgium', 350],
            ['2011', 'Q3', 'Belgium', 450],
            ['2011', 'Q4', 'Belgium', 500],
            ['2010', 'Q1', 'UK', 690],
            ['2010', 'Q2', 'UK', 610],
            ['2010', 'Q3', 'UK', 620],
            ['2010', 'Q4', 'UK', 600],
            ['2011', 'Q1', 'UK', 720],
            ['2011', 'Q2', 'UK', 650],
            ['2011', 'Q3', 'UK', 580],
            ['2011', 'Q4', 'UK', 510],
            ['2010', 'Q1', 'France', 510],
            ['2010', 'Q2', 'France', 490],
            ['2010', 'Q3', 'France', 460],
            ['2010', 'Q4', 'France', 590],
            ['2011', 'Q1', 'France', 620],
            ['2011', 'Q2', 'France', 650],
            ['2011', 'Q3', 'France', 415],
            ['2011', 'Q4', 'France', 570],
        ];
        $spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A2');

        $table = new Table('A1:D33', 'Sales_Data');

        // Create Columns
        $table->getColumn('D')->setShowFilterButton(false);
        $table->getAutoFilter()->getColumn('A')
            ->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
            ->createRule()
            ->setRule(AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 2011)
            ->setRuleType(AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);

        $spreadsheet->getActiveSheet()->addTable($table);

        $outfile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($outfile);
        $spreadsheet->disconnectWorksheets();

        // Make sure Reader handles row visibility properly.
        $reader = new XlsxReader();
        $spreadsheet2 = $reader->load($outfile);
        self::assertFalse($spreadsheet2->getActiveSheet()->getRowDimension(5)->getVisible());
        self::assertTrue($spreadsheet2->getActiveSheet()->getRowDimension(6)->getVisible());
        $spreadsheet2->disconnectWorksheets();

        // Make sure filterColumn tags are children of autoFilter.
        $file = 'zip://';
        $file .= $outfile;
        $file .= '#xl/tables/table1.xml';
        $data = file_get_contents($file);
        unlink($outfile);
        $expected = '<autoFilter ref="A1:D33">'
            . '<filterColumn colId="0">'
            . '<customFilters>'
            . '<customFilter operator="greaterThanOrEqual" val="2011"/>'
            . '</customFilters>'
            . '</filterColumn>'
            . '<filterColumn colId="3" hiddenButton="1"/>'
            . '</autoFilter>';
        self::assertStringContainsString($expected, $data);
    }
}
