<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use DateTime;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class XlsGifBmpTest extends AbstractFunctional
{
    private string $filename = '';

    protected function tearDown(): void
    {
        if ($this->filename) {
            unlink($this->filename);
        }
        $this->filename = '';
    }

    public function testBmp(): void
    {
        $pgmstart = (float) (new DateTime())->format('U');
        $spreadsheet = new Spreadsheet();
        $filstart = $spreadsheet->getProperties()->getModified();
        self::assertLessThanOrEqual($filstart, $pgmstart);

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters B, M, and P');
        $drawing->setDescription('Handwritten B, M, and P');
        $drawing->setPath(__DIR__ . '/../../../../samples/images/bmp.bmp');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $creationDatestamp = $reloadedSpreadsheet->getProperties()->getCreated();
        $filstart = $creationDatestamp;
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $worksheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($worksheet->getDrawingCollection() as $drawing) {
            $mimeType = ($drawing instanceof MemoryDrawing) ? $drawing->getMimeType() : 'notmemorydrawing';
            self::assertEquals('image/png', $mimeType);
        }
        $pgmend = (float) (new DateTime())->format('U');

        self::assertLessThanOrEqual($pgmend, $pgmstart);
        self::assertLessThanOrEqual($pgmend, $filstart);
        self::assertLessThanOrEqual($filstart, $pgmstart);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testGif(): void
    {
        $spreadsheet = new Spreadsheet();

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters G, I, and G');
        $drawing->setDescription('Handwritten G, I, and F');
        $drawing->setPath(__DIR__ . '/../../../../samples/images/gif.gif');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $worksheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($worksheet->getDrawingCollection() as $drawing) {
            $mimeType = ($drawing instanceof MemoryDrawing) ? $drawing->getMimeType() : 'notmemorydrawing';
            self::assertEquals('image/png', $mimeType);
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testGifIssue4112(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheet = new Worksheet($spreadsheet, 'Insured List');
        $spreadsheet->addSheet($sheet, 0);

        // Add a drawing to the worksheet
        $drawing = new Drawing();
        $drawing->setName('Letters G, I, and G');
        $drawing->setDescription('Handwritten G, I, and F');
        $drawing->setPath(__DIR__ . '/../../../../samples/images/gif.gif');
        $drawing->setHeight(36);
        $drawing->setWorksheet($sheet);
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $worksheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $worksheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($worksheet->getDrawingCollection() as $drawing) {
            $mimeType = ($drawing instanceof MemoryDrawing) ? $drawing->getMimeType() : 'notmemorydrawing';
            self::assertEquals('image/png', $mimeType);
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testInvalidTimestamp(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Expecting 8 byte string');
        \PhpOffice\PhpSpreadsheet\Shared\OLE::OLE2LocalDate(' ');
    }
}
