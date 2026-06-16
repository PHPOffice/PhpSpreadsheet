<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue993Test extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    public function testDrawingWithPoundUrl(): void
    {
        $path = 'samples/images/blue_square.png';
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('sheet1');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('sheet2');
        $drawing = new Drawing();
        $drawing->setWorksheet($sheet1);
        $drawing->setName('Blue Square');
        $drawing->setPath($path);
        $drawing->setCoordinates('E1');
        $drawing->setCoordinates2('F5');
        $hyperlink = new Hyperlink('#sheet2!C3', 'Click here');
        $drawing->setHyperlink($hyperlink);

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/drawings/_rels/drawing1.xml.rels';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString('<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="#sheet2!C3"/>', $data, 'url unchanged by write');

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outputFile);
        $rsheet1 = $reloadedSpreadsheet->getSheetByName('sheet1');
        self::assertNotNull($rsheet1);
        $drawings = $rsheet1->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertSame('sheet://sheet2!C3', $drawing0?->getHyperlink()?->getUrl(), 'url was changed on read');
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDrawingWithSheetUrl(): void
    {
        $path = 'samples/images/blue_square.png';
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('sheet1');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('sheet2');
        $drawing = new Drawing();
        $drawing->setWorksheet($sheet1);
        $drawing->setName('Blue Square');
        $drawing->setPath($path);
        $drawing->setCoordinates('E1');
        $drawing->setCoordinates2('F5');
        $hyperlink = new Hyperlink('sheet://sheet2!C3', 'Click here');
        $drawing->setHyperlink($hyperlink);

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFile;
        $file .= '#xl/drawings/_rels/drawing1.xml.rels';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertStringContainsString('<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="#sheet2!C3"/>', $data, 'url changed by write');

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outputFile);
        $rsheet1 = $reloadedSpreadsheet->getSheetByName('sheet1');
        self::assertNotNull($rsheet1);
        $drawings = $rsheet1->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing0 = $drawings[0];
        self::assertSame('sheet://sheet2!C3', $drawing0?->getHyperlink()?->getUrl(), 'url was changed back on read');
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
