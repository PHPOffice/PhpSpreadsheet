<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class TransparentDrawingsTest extends AbstractFunctional
{
    /**
     * Save and load XLSX with 2-cell anchor drawing with transparency.
     */
    public function testTwoCellAnchorTransparent(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add gif image that coordinates is two cell anchor.
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertEquals($drawing->getWidth(), 100);
        self::assertEquals($drawing->getHeight(), 100);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(30);
        $drawing->setOffsetY(10);
        $drawing->setCoordinates2('E8');
        $drawing->setOffsetX2(-50);
        $drawing->setOffsetY2(-20);
        $drawing->setOpacity(40000);
        $drawing->setWorksheet($sheet);

        // Write file
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();

        // Check image coordinates.
        $drawingCollection = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection);
        $drawing = $drawingCollection[0];
        self::assertNotNull($drawing);

        self::assertSame(100, $drawing->getWidth());
        self::assertSame(100, $drawing->getHeight());
        self::assertSame('A1', $drawing->getCoordinates());
        self::assertSame(30, $drawing->getOffsetX());
        self::assertSame(10, $drawing->getOffsetY());
        self::assertSame('E8', $drawing->getCoordinates2());
        self::assertSame(-50, $drawing->getOffsetX2());
        self::assertSame(-20, $drawing->getOffsetY2());
        self::assertSame(40000, $drawing->getOpacity());
        self::assertSame($rsheet, $drawing->getWorksheet());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Save and load XLSX with 1-cell anchor drawing with transparency.
     */
    public function testOneCellAnchorTransparent(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add gif image that coordinates is two cell anchor.
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        //self::assertEquals($drawing->getWidth(), 100);
        //self::assertEquals($drawing->getHeight(), 100);
        $drawing->setCoordinates('A1');
        $drawing->setOpacity(40000);
        $drawing->setWorksheet($sheet);

        // Write file
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();

        // Check image coordinates.
        $drawingCollection = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawingCollection);
        $drawing = $drawingCollection[0];
        self::assertNotNull($drawing);

        self::assertSame(100, $drawing->getWidth());
        self::assertSame(100, $drawing->getHeight());
        self::assertSame('A1', $drawing->getCoordinates());
        self::assertSame(40000, $drawing->getOpacity());
        self::assertSame($rsheet, $drawing->getWorksheet());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
