<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class WorksheetInfoNamesTest extends TestCase
{
    public function testListWorksheetInfo(): void
    {
        $filename = 'tests/data/Reader/XLSX/rowColumnAttributeTest.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetInfo($filename);

        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'F',
                'lastColumnIndex' => 5,
                'totalRows' => '6',
                'totalColumns' => 6,
                'sheetState' => 'visible',
            ],
        ];

        self::assertEquals($expected, $actual);
    }

    public function testListWorksheetInfoNamespace(): void
    {
        $filename = 'tests/data/Reader/XLSX/namespaces.xlsx';
        $file = 'zip://';
        $file .= $filename;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<x:workbook ', $data);
        }
        $reader = new Xlsx();
        $actual = $reader->listWorksheetInfo($filename);

        $expected = [
            [
                'worksheetName' => 'transactions',
                'lastColumnLetter' => 'K',
                'lastColumnIndex' => 10,
                'totalRows' => 2,
                'totalColumns' => 11,
                'sheetState' => 'visible',
            ],
        ];

        self::assertEquals($expected, $actual);
    }

    public function testListWorksheetNames(): void
    {
        $filename = 'tests/data/Reader/XLSX/rowColumnAttributeTest.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetNames($filename);

        $expected = ['Sheet1'];

        self::assertEquals($expected, $actual);
    }

    public function testListWorksheetNamesNamespace(): void
    {
        $filename = 'tests/data/Reader/XLSX/namespaces.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetNames($filename);

        $expected = ['transactions'];

        self::assertEquals($expected, $actual);
    }

    public function testListWorksheetNamesChartSheet(): void
    {
        $filename = 'tests/data/Reader/XLSX/ChartSheet.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetNames($filename);

        $expected = ['Sheet1', 'Chart1'];

        self::assertEquals($expected, $actual);
    }

    public function testListWorksheetInfoChartSheet(): void
    {
        $filename = 'tests/data/Reader/XLSX/ChartSheet.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetInfo($filename);

        $chartSheetInfo = $actual[1];

        self::assertSame('Chart1', $chartSheetInfo['worksheetName']);
        self::assertSame(-1, $chartSheetInfo['lastColumnIndex']);
        self::assertSame(0, $chartSheetInfo['totalRows']);
        self::assertSame(0, $chartSheetInfo['totalColumns']);
    }

    public function testListWorksheetMissingRows(): void
    {
        $filename = 'tests/data/Reader/XLSX/issue.3255.xlsx';
        $reader = new Xlsx();
        $actual = $reader->listWorksheetInfo($filename);
        self::assertSame(4, $actual[0]['totalColumns']);
        self::assertSame(1048576, $actual[0]['totalRows']);
        $reader->setReadEmptyCells(false);
        $actual = $reader->listWorksheetInfo($filename);
        self::assertSame(3, $actual[0]['totalColumns'], 'all cells in D have no data');
        self::assertSame(15, $actual[0]['totalRows'], 'rows 16 and 1048576 have no cells with data');
    }
}
