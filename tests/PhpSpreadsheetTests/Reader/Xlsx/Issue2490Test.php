<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2490Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.2490.xlsx';

    private static string $testbook3093 = 'tests/data/Reader/XLSX/issue.3093.xlsx';

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
            self::assertSame(64, substr_count($data, '<rgbColor'));
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

    public function testPreliminaries3093(): void
    {
        $file = 'zip://';
        $file .= self::$testbook3093;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected color index tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<colors><indexedColors><rgbColor rgb="ff000000"/>', $data);
            self::assertSame(15, substr_count($data, '<rgbColor'));
        }
    }

    public function testIssue3093(): void
    {
        // Same as above, except with fewer than 64 entries.
        // (And with colors in lowercase hex and alpha set to ff.)
        $filename = self::$testbook3093;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('ffc0c0c0', $sheet->getCell('B2')->getStyle()->getFill()->getStartColor()->getArgb());
        self::assertSame('ffffff00', $sheet->getCell('D2')->getStyle()->getFill()->getStartColor()->getArgb());
        self::assertSame('ffdfa7a6', $sheet->getCell('F2')->getStyle()->getFill()->getStartColor()->getArgb());
        self::assertSame('ff7ba0cd', $sheet->getCell('H2')->getStyle()->getFill()->getStartColor()->getArgb());
        $spreadsheet->disconnectWorksheets();
    }
}
