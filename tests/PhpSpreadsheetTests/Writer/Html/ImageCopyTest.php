<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class ImageCopyTest extends Functional\AbstractFunctional
{
    /** @var string */
    private $xlsxFile = '';

    protected function tearDown(): void
    {
        if ($this->xlsxFile !== '') {
            unlink($this->xlsxFile);
            $this->xlsxFile = '';
        }
    }

    public function testImageCopyXls(): void
    {
        $file = 'samples/templates/27template.xls';
        $reader = new XlsReader();
        $reloadedSpreadsheet = $reader->load($file);

        $writer = new Html($reloadedSpreadsheet);
        $writer->writeAllSheets();
        self::assertFalse($writer->getEmbedImages());
        $html = $writer->generateHTMLAll();
        self::assertSame(4, substr_count($html, '<img'));
        self::assertSame(0, substr_count($html, 'zip://'));
        // all 4 images converted to png
        self::assertSame(4, substr_count($html, 'data:image/png;base64'));

        $this->writeAndReload($reloadedSpreadsheet, 'Html');
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testImageCopyXlsx(): void
    {
        $file = 'samples/templates/27template.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($file);
        $this->xlsxFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->xlsxFile);
        $spreadsheet->disconnectWorksheets();
        $reader2 = new XlsxReader();
        $reloadedSpreadsheet = $reader2->load($this->xlsxFile);

        $writer = new Html($reloadedSpreadsheet);
        $writer->writeAllSheets();
        self::assertFalse($writer->getEmbedImages());
        $html = $writer->generateHTMLAll();
        self::assertSame(4, substr_count($html, '<img'));
        self::assertSame(0, substr_count($html, 'zip://'));
        // "gif" is actually stored as png in this file
        self::assertSame(2, substr_count($html, 'data:image/png;base64'));
        //self::assertSame(1, substr_count($html, 'data:image/gif;base64'));
        self::assertSame(2, substr_count($html, 'data:image/jpeg;base64'));

        $this->writeAndReload($reloadedSpreadsheet, 'Html');
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
