<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class Issue1319Test extends TestCase
{
    public static function testFromExcelFile(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.1319.bug2.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        $html = (string) preg_replace('/\r?\n\s+/m', '', $html);
        $spreadsheet->disconnectWorksheets();
        $expectedArray = [
            'table.sheet0 col.col0 { width:42pt }',
            'table.sheet0 col.col1 { width:42pt }',
            'table.sheet0 col.col2 { width:42pt }',
            'table.sheet0 col.col3 { width:42pt }',
            'table.sheet0 col.col4 { width:42pt }',
            '<tr class="row0"><td class="column0 style1 s style1" colspan="5" rowspan="3">test</td></tr>',
            '<tr class="row1"></tr>',
            '<tr class="row2"></tr>',
        ];
        foreach ($expectedArray as $expected) {
            self::assertStringContainsString($expected, $html);
        }
    }

    public static function testFromScratch(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('test');
        $sheet->mergeCells('A1:E3');
        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(18);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Following not needed when reading Excel, only from scratch.
        // columnDimension needed, rowDimension not needed
        //$sheet->getRowDimension(3);
        $sheet->getColumnDimension('E');

        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        $html = (string) preg_replace('/\r?\n\s+/m', '', $html);
        $spreadsheet->disconnectWorksheets();
        $expectedArray = [
            '<col class="col0" />',
            '<col class="col1" />',
            '<col class="col2" />',
            '<col class="col3" />',
            '<col class="col4" />',
            '<tr class="row0"><td class="column0 style1 s style0" colspan="5" rowspan="3">test</td></tr>',
            '<tr class="row1"></tr>',
            '<tr class="row2"></tr>',
        ];
        foreach ($expectedArray as $expected) {
            self::assertStringContainsString($expected, $html);
        }
    }
}
