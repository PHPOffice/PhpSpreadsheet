<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
        // inherit because cell style is inherit.
        // effectively protected because sheet is locked.
        self::assertTrue($sheet->cellExists('A12'));
        self::assertSame(Protection::PROTECTION_INHERIT, $sheet->getStyle('A12')->getProtection()->getLocked());
        self::assertTrue($sheet->getCell('A12')->isLocked());
        // unprotected because column is unprotected (no cell or row dimension style)
        self::assertFalse($sheet->cellExists('B12'));
        self::assertFalse($sheet->rowDimensionExists(12));
        self::assertTrue($sheet->columnDimensionExists('B'));
        $dxf = $sheet->getColumnDimension('B')->getXfIndex();
        if ($dxf === null) {
            self::fail('Unexpected null column xfIndex');
        } else {
            self::assertSame(Protection::PROTECTION_UNPROTECTED, $spreadsheet->getCellXfByIndex($dxf)->getProtection()->getLocked());
        }
        self::assertFalse($sheet->getCell('B12')->isLocked());
        // inherit because cell doesn't exist, no row dimension, no column dimension.
        // effectively protected because sheet is locked.
        self::assertFalse($sheet->cellExists('W8'));
        self::assertFalse($sheet->rowDimensionExists(8));
        self::assertFalse($sheet->columnDimensionExists('W'));
        self::assertTrue($sheet->getCell('W8')->isLocked());
        // inherit because cell doesn't exist, row dimension exists without style, no column dimension.
        // effectively protected because sheet is locked.
        self::assertFalse($sheet->cellExists('X11'));
        self::assertTrue($sheet->rowDimensionExists(11));
        $dxf = $sheet->getRowDimension(11)->getXfIndex();
        self::assertNull($dxf);
        self::assertFalse($sheet->columnDimensionExists('X'));
        self::assertTrue($sheet->getCell('X11')->isLocked());

        $sheet = $spreadsheet->getSheetByNameOrThrow('Welcome');
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $draw0 = $drawings[0] ?? new Drawing();
        self::assertSame('Picture 1', $draw0->getName());
        self::assertSame('C3', $draw0->getCoordinates());
        self::assertSame('C10', $draw0->getCoordinates2());
        self::assertSame('oneCell', $draw0->getEditAs());
        $spreadsheet->disconnectWorksheets();
    }
}
