<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingInCell extends AbstractFunctional
{
    public function testPictureInCell(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
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

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
