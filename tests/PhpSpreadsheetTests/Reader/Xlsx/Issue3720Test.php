<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Issue3720Test extends \PHPUnit\Framework\TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3720.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/_rels/workbook.xml.rels';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<ns3:Relationships ', $data);
        }
    }

    public function testInfo(): void
    {
        $reader = new Xlsx();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info1 = $workSheetInfo[1];
        self::assertEquals('Welcome', $info1['worksheetName']);
        self::assertEquals('H', $info1['lastColumnLetter']);
        self::assertEquals(7, $info1['lastColumnIndex']);
        self::assertEquals(49, $info1['totalRows']);
        self::assertEquals(8, $info1['totalColumns']);
    }

    public function testSheetNames(): void
    {
        $reader = new Xlsx();
        $worksheetNames = $reader->listWorksheetNames(self::$testbook);
        $expected = [
            'Data',
            'Welcome',
            'Sheet 1',
            'Sheet 2',
            'Sheet 3',
            'Sheet 4',
            'Sheet 5',
            'Sheet 6',
            'Sheet 7',
            'Sheet 8',
            'Sheet 9',
            'Sheet 10',
        ];
        self::assertEquals($expected, $worksheetNames);
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheets = $spreadsheet->getAllSheets();
        self::assertCount(12, $sheets);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet 1');
        $sheetProtection = $sheet->getProtection();
        self::assertTrue($sheetProtection->getSheet());
        self::assertSame(' FILL IN WHITE CELLS ONLY', $sheet->getCell('B3')->getValue());
        // inherit because no cell, row, or column style.
        // effectively protected because sheet is locked.
        self::assertTrue($sheet->getCell('A12')->isLocked());
        // unprotected because column is unprotected (no cell or row style)
        self::assertFalse($sheet->getCell('B12')->isLocked());
        // inherit because cell has style with protection omitted.
        // effectively protected because sheet is locked.
        self::assertTrue($sheet->getCell('B11')->isLocked());
        $sheet = $spreadsheet->getSheetByNameOrThrow('Welcome');
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $failmsg = '';
        if (isset($drawings[0])) {
            $draw0 = $drawings[0];
            if (method_exists($draw0, 'getPath')) {
                self::assertSame('image1.jpeg', basename($draw0->getPath()));
            } else {
                $failmsg = 'unexpected missing getPath method';
            }
        } else {
            $failmsg = 'unexpected missing array item 0';
        }
        $spreadsheet->disconnectWorksheets();
        if ($failmsg !== '') {
            self::fail($failmsg);
        }
    }
}
