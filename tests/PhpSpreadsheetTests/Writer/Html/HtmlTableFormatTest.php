<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlTableFormatTest extends TestCase
{
    private string $data = '';

    private function extractCell(string $coordinate): string
    {
        [$column, $row] = Coordinate::indexesFromString($coordinate);
        --$column;
        --$row;
        // extract row into $matches
        $match = preg_match('~<tr class="row' . $row . '".+?</tr>~s', $this->data, $matches);
        if ($match !== 1) {
            return 'unable to match row';
        }
        $rowData = $matches[0];
        // extract cell into $matches
        $match = preg_match('~<td class="column' . $column . ' .+?</td>~s', $rowData, $matches);
        if ($match !== 1) {
            return 'unable to match column';
        }

        return $matches[0];
    }

    public function testHtmlTableFormatOutput(): void
    {
        $file = 'samples/templates/TableFormat.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setTableFormats(true);
        self::assertTrue($writer->getTableFormats());
        $this->data = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        $expectedMatches = [
            ['J1', 'background-color:#145F82;">Sep<', 'table style for header row cell J1'],
            ['J2', 'background-color:#C0E4F5;">110<', 'table style for cell J2'],
            ['I3', 'background-color:#82CAEB;">70<', 'table style for cell I3'],
            ['J3', 'background-color:#82CAEB;">70<', 'table style for cell J3'], // as conditional calculations are off
            ['K3', 'background-color:#82CAEB;">70<', 'table style for cell K3'],
            ['J4', 'background-color:#C0E4F5;">1<', 'table style for cell J4'],
            ['J5', 'background-color:#82CAEB;">1<', 'table style for cell J5'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testBuiltinApplied(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $dataArray = [
            ['Year', 'Quarter', 'Country', 'Sales'],
            ['2010', 'Q1', 'United States', 790],
            ['2010', 'Q2', 'United States', 730],
            ['2010', 'Q3', 'United States', 860],
            ['2010', 'Q4', 'United States', 850],
        ];
        $sheet->fromArray($dataArray);
        $table = new Table('A1:D5', 'Sales_Data');
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $tableStyle->setShowRowStripes(true);
        $tableStyle->setShowColumnStripes(true);
        $tableStyle->setShowFirstColumn(true);
        $tableStyle->setShowLastColumn(true);
        $table->setStyle($tableStyle);
        $sheet->addTable($table);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setTableFormats(true); // format all tables using a default style for unstyled
        $this->data = $writer->generateHtmlAll();
        $expectedMatches = [
            ['A1', '<td class="column0 style0 s" style="color:#FFFFFF;background-color:#000000;">Year</td>', 'table style for header row cell A1'],
            ['B2', '<td class="column1 style0 s" style="color:#000000;background-color:#D9D9D9;">Q1</td>', 'table style for cell B2'],
            ['C3', '<td class="column2 style0 s">United States</td>', 'table style for cell C3'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testBuiltinNotApplied(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $dataArray = [
            ['Year', 'Quarter', 'Country', 'Sales'],
            ['2010', 'Q1', 'United States', 790],
            ['2010', 'Q2', 'United States', 730],
            ['2010', 'Q3', 'United States', 860],
            ['2010', 'Q4', 'United States', 850],
        ];
        $sheet->fromArray($dataArray);
        $table = new Table('A1:D5', 'Sales_Data');
        $tableStyle = new TableStyle();
        $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
        $tableStyle->setShowRowStripes(true);
        $tableStyle->setShowColumnStripes(true);
        $tableStyle->setShowFirstColumn(true);
        $tableStyle->setShowLastColumn(true);
        $table->setStyle($tableStyle);
        $sheet->addTable($table);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setTableFormats(true, false); // format styled tables but not unstyled
        $this->data = $writer->generateHtmlAll();
        $expectedMatches = [
            ['A1', '<td class="column0 style0 s">Year</td>', 'table style for header row cell A1'],
            ['B2', '<td class="column1 style0 s">Q1</td>', 'table style for cell B2'],
            ['C3', '<td class="column2 style0 s">United States</td>', 'table style for cell C3'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
        $spreadsheet->disconnectWorksheets();
    }
}
