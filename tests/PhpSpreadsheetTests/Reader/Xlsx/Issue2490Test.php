<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2490Test extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue.2490.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected color index tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<colors><indexedColors><rgbColor rgb="00000000"/>', $data);
        }
    }

    public function testIssue2490(): void
    {
        // Spreadsheet with its own color palette.
        $filename = self::$testbook;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('00FFFFFF', $sheet->getCell('A3')->getStyle()->getFill()->getStartColor()->getArgb());
        self::assertSame('00F0FBFF', $sheet->getCell('A1')->getStyle()->getFill()->getStartColor()->getArgb());
        self::assertSame('00F0F0F0', $sheet->getCell('B1')->getStyle()->getFill()->getStartColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }
}
