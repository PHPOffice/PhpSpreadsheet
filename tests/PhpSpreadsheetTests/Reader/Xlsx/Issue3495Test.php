<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3495Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.3495d.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file2 = $file;
        $file .= '#xl/styles.xml';
        $file2 .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            // default style plus one other style
            self::assertStringContainsString(
                '<cellXfs count="2">'
                . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" quotePrefix="1"/>',
                $data
            );
        }
        $data = file_get_contents($file2);
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            // cells B1, C1, D1 all nominally use quotePrefix s="1"
            self::assertStringContainsString('<c r="B1" s="1">', $data);
            self::assertStringContainsString('<c r="C1" s="1" t="s">', $data);
            self::assertStringContainsString('<c r="D1" s="1" t="s">', $data);
        }
    }

    public function testFormulaDespiteQuotePrefix(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=2+3', $sheet->getCell('B1')->getValue());
        self::assertSame('=1+2', $sheet->getCell('C1')->getValue());
        self::assertSame('3', $sheet->getCell('D1')->getValue());
        self::assertSame(5, $sheet->getCell('B1')->getCalculatedValue());
        self::assertSame('=1+2', $sheet->getCell('C1')->getCalculatedValue());
        self::assertSame('3', $sheet->getCell('D1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
