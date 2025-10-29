<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class DrawingInCellTest extends TestCase
{
    public function testPictureInCell(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $sheet = $spreadsheet->getSheet(0);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(2, $drawings);

        if ($drawings[0] === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawings[0]->getType());
            self::assertSame('B2', $drawings[0]->getCoordinates());
            self::assertSame(0, $drawings[0]->getOffsetX());
            self::assertSame(0, $drawings[0]->getOffsetY());
            self::assertSame(296, $drawings[0]->getWidth());
            self::assertSame(154, $drawings[0]->getHeight());
            self::assertSame(296, $drawings[0]->getImageWidth());
            self::assertSame(154, $drawings[0]->getImageHeight());
        }

        self::assertSame($drawings[0], $sheet->getCell('B2')->getValue());

        $sheet = $spreadsheet->getSheet(1);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(1, $drawings);

        if ($drawings[0] === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawings[0]->getType());
            self::assertSame('D7', $drawings[0]->getCoordinates());
            self::assertSame(0, $drawings[0]->getOffsetX());
            self::assertSame(0, $drawings[0]->getOffsetY());
            self::assertSame(413, $drawings[0]->getWidth());
            self::assertSame(218, $drawings[0]->getHeight());
            self::assertSame(413, $drawings[0]->getImageWidth());
            self::assertSame(218, $drawings[0]->getImageHeight());
        }

        $spreadsheet->disconnectWorksheets();
    }
}
