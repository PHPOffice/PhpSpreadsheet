<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class ConditionalNoFormatSetTest extends TestCase
{
    public function testNoFormatTest(): void
    {
        $testfile = 'tests/data/Reader/XLSX/issue.3202.xlsx';
        $file = 'zip://';
        $file .= $testfile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        $expected = '<conditionalFormatting sqref="A1:A5">'
            . '<cfRule type="expression" dxfId="1" priority="2" stopIfTrue="1">'
            . '<formula>$A1&gt;5</formula>'
            . '</cfRule>'
            . '<cfRule type="expression" dxfId="0" priority="3">'
            . '<formula>$A1&gt;1</formula>'
            . '</cfRule>'
            . '<cfRule type="expression" priority="1" stopIfTrue="1">'
            . '<formula>$A1=3</formula>'
            . '</cfRule>'
            . '</conditionalFormatting>';
        self::assertStringContainsString($expected, $data);

        $reader = new XlsxReader();
        $spreadsheet = $reader->load($testfile);
        $outfile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($outfile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $outfile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        unlink($outfile);

        $expected = '<conditionalFormatting sqref="A1:A5">'
            . '<cfRule type="expression" priority="1" stopIfTrue="1">'
            . '<formula>$A1=3</formula>'
            . '</cfRule>'
            . '<cfRule type="expression" dxfId="1" priority="2" stopIfTrue="1">'
            . '<formula>$A1&gt;5</formula>'
            . '</cfRule>'
            . '<cfRule type="expression" dxfId="2" priority="3">'
            . '<formula>$A1&gt;1</formula>'
            . '</cfRule>'
            . '</conditionalFormatting>';
        self::assertStringContainsString($expected, $data);
    }
}
