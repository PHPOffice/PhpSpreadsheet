<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use RuntimeException;

class XlsGifBmpTest extends AbstractFunctional
{
    private $filename = '';

    protected function tearDown(): void
    {
        if ($this->filename) {
            unlink($this->filename);
        }
        $this->filename = '';
    }

    public function testBmp(): void
    {
        $pgmstart = time();
        $spreadsheet = new Spreadsheet();
        $filstart = $spreadsheet->getProperties()->getModified();
        self::assertLessThanOrEqual($filstart, $pgmstart);

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters B, M, and P');
        $drawing->setDescription('Handwritten B, M, and P');
        $drawing->setPath(__DIR__ . '../../../../../samples/images/bmp.bmp');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $creationDatestamp = $reloadedSpreadsheet->getProperties()->getCreated();
        $filstart = $creationDatestamp;
        $pSheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $pSheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($pSheet->getDrawingCollection() as $drawing) {
            self::assertTrue($drawing instanceof MemoryDrawing);
            self::assertEquals('image/png', $drawing->getMimeType());
        }
        $pgmend = time();

        self::assertLessThanOrEqual($pgmend, $pgmstart);
        self::assertLessThanOrEqual($pgmend, $filstart);
        self::assertLessThanOrEqual($filstart, $pgmstart);
    }

    public function testGif(): void
    {
        $spreadsheet = new Spreadsheet();

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters G, I, and G');
        $drawing->setDescription('Handwritten G, I, and F');
        $drawing->setPath(__DIR__ . '../../../../../samples/images/gif.gif');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $pSheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $pSheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($pSheet->getDrawingCollection() as $drawing) {
            self::assertTrue($drawing instanceof MemoryDrawing);
            self::assertEquals('image/png', $drawing->getMimeType());
        }
    }

    public function testGifNoGd(): void
    {
        $this->expectException(RuntimeException::class);
        $spreadsheet = new Spreadsheet();

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters G, I, and G');
        $drawing->setDescription('Handwritten G, I, and F');
        $drawing->setPath(__DIR__ . '../../../../../samples/images/gif.gif');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $writer = new XlsWriter($spreadsheet);
        $writer->setSimulateNoGd(true);
        $this->filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer->save($this->filename);
    }

    public function testNoImagesNoGd(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);

        $writer = new XlsWriter($spreadsheet);
        $writer->setSimulateNoGd(true);
        $oufil = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer->save($oufil);
        $reader = new XlsReader();
        $rdobj = $reader->load($oufil);
        unlink($oufil);
        $rdsheet = $rdobj->getActiveSheet();
        self::assertEquals(1, $rdsheet->getCell('A1')->getValue());
    }

    public function testInvalidTimestamp(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);
        \PhpOffice\PhpSpreadsheet\Shared\OLE::OLE2LocalDate(' ');
    }
}
