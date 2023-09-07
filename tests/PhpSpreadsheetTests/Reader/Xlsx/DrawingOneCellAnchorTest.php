<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PHPUnit\Framework\TestCase;

class DrawingOneCellAnchorTest extends TestCase
{
    public function testGetDrawing(): void
    {
        $filename = __DIR__ . '/../../../data/Reader/XLSX/drawingOneCellAnchor.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(1, $collection);

        /** @var BaseDrawing $drawing */
        $drawing = $collection[0];
        self::assertEquals('A2', $drawing->getCoordinates());
        self::assertEquals(10, $drawing->getOffsetX());
        self::assertEquals(10, $drawing->getOffsetY());
        self::assertEquals(150, $drawing->getHeight());
        self::assertEquals(150, $drawing->getWidth());
    }
}
