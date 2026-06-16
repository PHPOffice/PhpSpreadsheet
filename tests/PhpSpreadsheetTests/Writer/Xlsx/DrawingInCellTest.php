<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingInCellTest extends AbstractFunctional
{
    public function testCreateFileWithPictureInCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $objDrawing = new Drawing();
        $objDrawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        $worksheet->getCell('C2')->setValue($objDrawing);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(0);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(1, $drawings);

        if ($drawings[0] === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawings[0]->getType());
            self::assertSame('C2', $drawings[0]->getCoordinates());
            self::assertSame(0, $drawings[0]->getOffsetX());
            self::assertSame(0, $drawings[0]->getOffsetY());
            self::assertSame(100, $drawings[0]->getWidth());
            self::assertSame(100, $drawings[0]->getHeight());
            self::assertSame(100, $drawings[0]->getImageWidth());
            self::assertSame(100, $drawings[0]->getImageHeight());
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testPictureInCell(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(0);
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

        $sheet = $reloadedSpreadsheet->getSheet(1);
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

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testWriteNewPictureInCell(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $objDrawing = new Drawing();
        $objDrawing->setPath('tests/data/Writer/XLSX/blue_square.png');

        $sheet = $spreadsheet->getSheet(1);
        $sheet->getCell('C10')->setValue($objDrawing);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(1);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(2, $drawings);

        /** @var ?Drawing $drawing */
        $drawing = $sheet->getCell('C10')->getValue();

        if ($drawing === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawing->getType());
            self::assertSame('C10', $drawing->getCoordinates());
            self::assertSame(100, $drawing->getWidth());
            self::assertSame(100, $drawing->getHeight());
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testMoveImageInCell(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getSheet(1);

        /** @var ?Drawing $drawing */
        $drawing = $sheet->getCell('D7')->getValue();
        if ($drawing === null) {
            self::fail('Unexpected null drawing');
        }
        $originalWidth = $drawing->getWidth();
        $originalHeight = $drawing->getHeight();
        $sheet->getCell('D7')->setValue(null);
        $sheet->getCell('D8')->setValue($drawing);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(1);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(1, $drawings);

        /** @var ?Drawing $drawing */
        $drawing = $sheet->getCell('D8')->getValue();

        if ($drawing === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawing->getType());
            self::assertSame('D8', $drawing->getCoordinates());
            self::assertSame($originalWidth, $drawing->getWidth());
            self::assertSame($originalHeight, $drawing->getHeight());
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testWriteSamePictureInCellAndAsFloating(): void
    {
        $file = 'tests/data/Reader/XLSX/drawing_in_cell.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);

        $objDrawing = new Drawing();
        $objDrawing->setPath('tests/data/Writer/XLSX/blue_square.png');

        $sheet = $spreadsheet->getSheet(1);
        $sheet->getCell('C10')->setValue($objDrawing);

        $objFloatingDrawing = new Drawing();
        $objFloatingDrawing->setPath('tests/data/Writer/XLSX/blue_square.png');

        $coordinates = $sheet->getCell('B5')->getCoordinate();
        $objFloatingDrawing->setCoordinates($coordinates);
        $objFloatingDrawing->setOffsetX(1);
        $objFloatingDrawing->setOffsetY(1);
        $objFloatingDrawing->setWorksheet($sheet);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(1);
        $drawings = $sheet->getInCellDrawingCollection();
        self::assertCount(2, $drawings);

        /** @var ?Drawing $drawing */
        $drawing = $sheet->getCell('C10')->getValue();

        if ($drawing === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertSame(IMAGETYPE_PNG, $drawing->getType());
            self::assertSame('C10', $drawing->getCoordinates());
            self::assertSame(100, $drawing->getWidth());
            self::assertSame(100, $drawing->getHeight());
            self::assertTrue($drawing->isInCell());
        }

        $floatingDrawings = $sheet->getDrawingCollection();
        $floatingDrawing = $floatingDrawings[0];

        if ($floatingDrawing === null) {
            self::fail('Unexpected null drawing');
        } else {
            self::assertCount(1, $floatingDrawings);
            self::assertSame('B5', $floatingDrawing->getCoordinates());
            self::assertFalse($floatingDrawing->isInCell());
            self::assertNull($sheet->getCell('B5')->getValue());
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
