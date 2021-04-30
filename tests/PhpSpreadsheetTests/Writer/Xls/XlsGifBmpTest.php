<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class XlsGifBmpTest extends AbstractFunctional
{
    /**
     * @var string
     */
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
        $drawing->setPath(__DIR__ . '/../../../../samples/images/bmp.bmp');
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
            // See if Scrutinizer approves this
            $mimeType = ($drawing instanceof MemoryDrawing) ? $drawing->getMimeType() : 'notmemorydrawing';
            self::assertEquals('image/png', $mimeType);
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
        $drawing->setPath(__DIR__ . '/../../../../samples/images/gif.gif');
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $pSheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $pSheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($pSheet->getDrawingCollection() as $drawing) {
            $mimeType = ($drawing instanceof MemoryDrawing) ? $drawing->getMimeType() : 'notmemorydrawing';
            self::assertEquals('image/png', $mimeType);
        }
    }

    public function testInvalidTimestamp(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Reader\Exception::class);
        \PhpOffice\PhpSpreadsheet\Shared\OLE::OLE2LocalDate(' ');
    }
}
