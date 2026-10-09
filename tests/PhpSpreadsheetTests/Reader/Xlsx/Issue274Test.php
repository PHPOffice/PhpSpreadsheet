<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\Metafile;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

/**
 * Xlsx file with an EMF image drawn by EMR_SETDIBITSTODEVICE records, converted to Xls.
 */
class Issue274Test extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.274.xlsx';

    public function testReadEmf(): void
    {
        $spreadsheet = (new Xlsx())->load(self::$testbook);
        $drawings = $spreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame(Metafile::TYPE_EMF, $drawing->getMetafileType());
        self::assertSame(131, $drawing->getImageWidth());
        self::assertSame(131, $drawing->getImageHeight());
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteXls(): void
    {
        $spreadsheet = (new Xlsx())->load(self::$testbook);

        // The EMF image is converted to PNG
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertInstanceOf(MemoryDrawing::class, $drawing);
        $image = $drawing->getImageResource();
        self::assertNotNull($image);
        self::assertSame(131, imagesx($image));
        self::assertSame(131, imagesy($image));
        // The black background of the EMF image is covered by the bitmap
        self::assertNotSame(0x000000, imagecolorat($image, 64, 64) & 0xFFFFFF);

        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testWriteOds(): void
    {
        $spreadsheet = (new Xlsx())->load(self::$testbook);

        // The EMF image is converted to PNG (LibreOffice draws it as a black square)
        $filename = File::temporaryFilename();
        (new OdsWriter($spreadsheet))->save($filename);
        $png = file_get_contents('zip://' . $filename . '#Pictures/image1.png');
        unlink($filename);
        self::assertNotFalse($png);
        $image = imagecreatefromstring($png);
        self::assertNotFalse($image);
        self::assertSame(131, imagesx($image));
        self::assertNotSame(0x000000, imagecolorat($image, 64, 64) & 0xFFFFFF);

        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteHtml(): void
    {
        $spreadsheet = (new Xlsx())->load(self::$testbook);
        $html = (new Html($spreadsheet))->generateHtmlAll();
        self::assertMatchesRegularExpression('~<img [^>]*src="data:image/png;base64,[^"]+"~', $html);
        $spreadsheet->disconnectWorksheets();
    }
}
