<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    public function testCloningWorksheetWithImages(): void
    {
        $gdImage = @imagecreatetruecolor(120, 20);
        $textColor = ($gdImage === false) ? false : imagecolorallocate($gdImage, 255, 255, 255);
        if ($gdImage === false || $textColor === false) {
            self::fail('imagecreatetruecolor or imagecolorallocate failed');
        } else {
            $spreadsheet = new Spreadsheet();
            $aSheet = $spreadsheet->getActiveSheet();
            imagestring($gdImage, 1, 5, 5, 'Created with PhpSpreadsheet', $textColor);

            $drawing = new MemoryDrawing();
            $drawing->setName('In-Memory image 1');
            $drawing->setDescription('In-Memory image 1');
            $drawing->setCoordinates('A1');
            $drawing->setImageResource($gdImage);
            $drawing->setRenderingFunction(
                MemoryDrawing::RENDERING_JPEG
            );
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setHeight(36);
            $drawing->setWorksheet($aSheet);

            $originDrawingCount = count($aSheet->getDrawingCollection());
            $clonedWorksheet = clone $aSheet;
            $clonedDrawingCount = count($clonedWorksheet->getDrawingCollection());

            self::assertEquals($originDrawingCount, $clonedDrawingCount);
            self::assertNotSame($aSheet, $clonedWorksheet);
            self::assertNotSame($aSheet->getDrawingCollection(), $clonedWorksheet->getDrawingCollection());
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function testChangeWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();

        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setPath('tests/data/Writer/XLSX/green_square.gif');
        self::assertEquals($drawing->getWidth(), 150);
        self::assertEquals($drawing->getHeight(), 150);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(30);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet1);

        try {
            $drawing->setWorksheet($sheet2);
            self::fail('Should throw exception when attempting set worksheet without specifying override');
        } catch (PhpSpreadsheetException $e) {
            self::assertStringContainsString('A Worksheet has already been assigned.', $e->getMessage());
        }
        self::assertSame($sheet1, $drawing->getWorksheet());
        self::assertCount(1, $sheet1->getDrawingCollection());
        self::assertCount(0, $sheet2->getDrawingCollection());
        $drawing->setWorksheet($sheet2, true);
        self::assertSame($sheet2, $drawing->getWorksheet());
        self::assertCount(0, $sheet1->getDrawingCollection());
        self::assertCount(1, $sheet2->getDrawingCollection());
    }

    public function testHeaderFooter(): void
    {
        $drawing1 = new HeaderFooterDrawing();
        $drawing1->setName('Blue Square');
        $drawing1->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertEquals($drawing1->getWidth(), 100);
        self::assertEquals($drawing1->getHeight(), 100);
        $drawing2 = new HeaderFooterDrawing();
        $drawing2->setName('Blue Square');
        $drawing2->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertSame($drawing1->getHashCode(), $drawing2->getHashCode());
        $drawing2->setOffsetX(100);
        self::assertNotEquals($drawing1->getHashCode(), $drawing2->getHashCode());
    }

    public function testSetWidthAndHeight(): void
    {
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertSame(100, $drawing->getWidth());
        self::assertSame(100, $drawing->getHeight());
        self::assertTrue($drawing->getResizeProportional());
        $drawing->setResizeProportional(false);
        $drawing->setWidthAndHeight(150, 200);
        self::assertSame(150, $drawing->getWidth());
        self::assertSame(200, $drawing->getHeight());
        $drawing->setResizeProportional(true);
        $drawing->setWidthAndHeight(300, 250);
        // width increase% more than height, so scale width
        self::assertSame(188, $drawing->getWidth());
        self::assertSame(250, $drawing->getHeight());
        $drawing->setResizeProportional(false);
        $drawing->setWidthAndHeight(150, 200);
        $drawing->setResizeProportional(true);
        // height increase% more than width, so scale height
        $drawing->setWidthAndHeight(175, 350);
        self::assertSame(175, $drawing->getWidth());
        self::assertSame(234, $drawing->getHeight());
    }
}
