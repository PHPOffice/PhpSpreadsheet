<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class Issue2501Test extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.2501.b.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected merge ranges
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<mergeCells count="3"><mergeCell ref="A:A"/><mergeCell ref="B:D"/><mergeCell ref="E2:E4"/></mergeCells>', $data);
        }
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet2.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected merged ranges
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<mergeCells count="3"><mergeCell ref="1:1"/><mergeCell ref="2:4"/><mergeCell ref="B5:D5"/></mergeCells>', $data);
        }
    }

    public function testIssue2501(): void
    {
        // Merged cell range specified as 1:1"
        $filename = self::$testbook;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Columns');
        $expected = [
            'A1:A1048576',
            'B1:D1048576',
            'E2:E4',
        ];
        self::assertSame($expected, array_values($sheet->getMergeCells()));
        $sheet = $spreadsheet->getSheetByNameOrThrow('Rows');
        $expected = [
            'A1:XFD1',
            'A2:XFD4',
            'B5:D5',
        ];
        self::assertSame($expected, array_values($sheet->getMergeCells()));

        $spreadsheet->disconnectWorksheets();
    }
}
