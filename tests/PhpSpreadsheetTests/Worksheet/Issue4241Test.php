<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PHPUnit\Framework\TestCase;

class Issue4241Test extends TestCase
{
    public function testIssue4241(): void
    {
        // setWorksheet needed to come after setPath
        $badPath = 'tests/data/Writer/XLSX/xgreen_square.gif';
        $goodPath = 'tests/data/Writer/XLSX/green_square.gif';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setWorksheet($sheet);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing0);
        self::assertSame('', $drawing0->getPath());
        self::assertSame('A1', $drawing0->getCoordinates());
        $maxRow = $sheet->getHighestDataRow();
        $maxCol = $sheet->getHighestDataColumn();
        self::assertSame(1, $maxRow);
        self::assertSame('A', $maxCol);

        $drawing->setCoordinates('E5');
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing0);
        self::assertSame('', $drawing0->getPath());
        self::assertSame('E5', $drawing0->getCoordinates());
        $maxRow = $sheet->getHighestDataRow();
        $maxCol = $sheet->getHighestDataColumn();
        self::assertSame(1, $maxRow);
        self::assertSame('A', $maxCol);

        $drawing->setPath($badPath, false);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing0);
        self::assertSame('', $drawing0->getPath());
        self::assertSame('E5', $drawing0->getCoordinates());
        $maxRow = $sheet->getHighestDataRow();
        $maxCol = $sheet->getHighestDataColumn();
        self::assertSame(1, $maxRow);
        self::assertSame('A', $maxCol);

        $drawing->setPath($goodPath);
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing0);
        self::assertSame($goodPath, $drawing0->getPath());
        self::assertSame('E5', $drawing0->getCoordinates());
        $maxRow = $sheet->getHighestDataRow();
        $maxCol = $sheet->getHighestDataColumn();
        self::assertSame(5, $maxRow);
        self::assertSame('E', $maxCol);

        $drawing->setCoordinates('G3');
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing0);
        self::assertSame($goodPath, $drawing0->getPath());
        self::assertSame('G3', $drawing0->getCoordinates());
        $maxRow = $sheet->getHighestDataRow();
        $maxCol = $sheet->getHighestDataColumn();
        self::assertSame(5, $maxRow);
        self::assertSame('G', $maxCol);

        $spreadsheet->disconnectWorksheets();
    }
}
