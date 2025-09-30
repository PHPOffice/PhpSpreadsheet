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
        $sheet = $spreadsheet->getActiveSheet();
        $drawingCollection = $sheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection);

        if ($drawingCollection[0] === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawingCollection[0]->getType());
            self::assertSame('B2', $drawingCollection[0]->getCoordinates());
            self::assertSame(0, $drawingCollection[0]->getOffsetX());
            self::assertSame(0, $drawingCollection[0]->getOffsetY());
            self::assertSame(296, $drawingCollection[0]->getWidth());
            self::assertSame(154, $drawingCollection[0]->getHeight());
            self::assertSame(296, $drawingCollection[0]->getImageWidth());
            self::assertSame(154, $drawingCollection[0]->getImageHeight());
        }

        $spreadsheet->disconnectWorksheets();
    }
}
