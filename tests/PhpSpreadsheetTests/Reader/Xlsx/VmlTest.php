<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class VmlTest extends TestCase
{
    private string $outfile1 = '';

    private string $outfile2 = '';

    protected function tearDown(): void
    {
        if ($this->outfile1 !== '') {
            unlink($this->outfile1);
            $this->outfile1 = '';
        }
        if ($this->outfile2 !== '') {
            unlink($this->outfile2);
            $this->outfile2 = '';
        }
    }

    public function testAddComments(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getComment('A1')->getText()->createText('top left cell');
        $writer = new XlsxWriter($spreadsheet);
        $this->outfile1 = File::temporaryFileName();
        $writer->save($this->outfile1);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $file = 'zip://' . $this->outfile1 . '#xl/worksheets/sheet1.xml';
        $sheetContents = file_get_contents($file) ?: '';
        self::assertStringContainsString('<legacyDrawing ', $sheetContents);
        $file = 'zip://' . $this->outfile1 . '#xl/drawings/vmlDrawing1.vml';
        $vmlContents = file_get_contents($file) ?: '';
        $count = substr_count($vmlContents, '<v:shape ');
        self::assertSame(1, $count);
        $count = substr_count($vmlContents, '<x:ClientData ObjectType="Note">');
        self::assertSame(1, $count);

        $spreadsheet2 = $reader->load($this->outfile1);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame('top left cell', $sheet2->getComment('A1')->getText()->getPlainText());
        self::assertNull($spreadsheet2->getLegacyDrawing($sheet2));
        $sheet2->getComment('H1')->getText()->createText('Show me');
        $sheet2->getComment('H2')->getText()->createText('Hide me');
        $sheet2->getComment('H1')->setVisible(true);
        $writer = new XlsxWriter($spreadsheet2);
        $this->outfile2 = File::temporaryFileName();
        $writer->save($this->outfile2);
        $spreadsheet2->disconnectWorksheets();

        $file = 'zip://' . $this->outfile2 . '#xl/worksheets/sheet1.xml';
        $sheetContents = file_get_contents($file) ?: '';
        self::assertStringContainsString('<legacyDrawing ', $sheetContents);
        $file = 'zip://' . $this->outfile2 . '#xl/drawings/vmlDrawing1.vml';
        $vmlContents = file_get_contents($file) ?: '';
        $count = substr_count($vmlContents, '<v:shape ');
        self::assertSame(3, $count);
        $count = substr_count($vmlContents, '<x:ClientData ObjectType="Note">');
        self::assertSame(3, $count);

        $reader = new XlsxReader();
        $spreadsheet3 = $reader->load($this->outfile2);
        $sheet3 = $spreadsheet3->getActiveSheet();
        self::assertSame('top left cell', $sheet3->getComment('A1')->getText()->getPlainText());
        self::assertSame('Show me', $sheet3->getComment('H1')->getText()->getPlainText());
        self::assertSame('Hide me', $sheet3->getComment('H2')->getText()->getPlainText());
        self::assertNull($spreadsheet3->getLegacyDrawing($sheet3));
        self::assertTrue($sheet3->getComment('H1')->getVisible());
        self::assertFalse($sheet3->getComment('H2')->getVisible());
        $spreadsheet3->disconnectWorksheets();
    }

    public function testDeleteNullLegacy(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getComment('A1')->getText()->createText('top left cell');
        self::assertNull($spreadsheet->getLegacyDrawing($sheet));
        $spreadsheet->deleteLegacyDrawing($sheet);
        $writer = new XlsxWriter($spreadsheet);
        $this->outfile1 = File::temporaryFileName();
        $writer->save($this->outfile1);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $file = 'zip://' . $this->outfile1 . '#xl/worksheets/sheet1.xml';
        $sheetContents = file_get_contents($file) ?: '';
        self::assertStringContainsString('<legacyDrawing ', $sheetContents);
        $file = 'zip://' . $this->outfile1 . '#xl/drawings/vmlDrawing1.vml';
        $vmlContents = file_get_contents($file) ?: '';
        $count = substr_count($vmlContents, '<v:shape ');
        self::assertSame(1, $count);
        $count = substr_count($vmlContents, '<x:ClientData ObjectType="Note">');
        self::assertSame(1, $count);

        $spreadsheet2 = $reader->load($this->outfile1);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertSame('top left cell', $sheet2->getComment('A1')->getText()->getPlainText());
        $spreadsheet2->disconnectWorksheets();
    }

    public function testAddCommentDeleteFormControls(): void
    {
        $infile = 'samples/Reader2/sampleData/formscomments.xlsx';
        $reader = new XlsxReader();
        $reader->setLoadSheetsOnly('FormsComments');
        $spreadsheet = $reader->load($infile);
        self::assertTrue(true);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('row1', $sheet->getCell('H1')->getValue());
        self::assertStringContainsString('Hello', $sheet->getComment('F1')->getText()->getPlainText());
        $vmlContents = $spreadsheet->getLegacyDrawing($sheet) ?? '';
        $count = substr_count($vmlContents, '<v:shape ');
        self::assertSame(4, $count);
        $count = substr_count($vmlContents, '<x:ClientData ');
        self::assertSame(4, $count);
        $count = substr_count($vmlContents, '<x:ClientData ObjectType="Note"');
        self::assertSame(1, $count);
        $spreadsheet->deleteLegacyDrawing($sheet);
        $sheet->getComment('F2')->getText()->createText('Goodbye');
        $writer = new XlsxWriter($spreadsheet);
        $this->outfile1 = File::temporaryFileName();
        $writer->save($this->outfile1);
        $spreadsheet->disconnectWorksheets();

        $reader2 = new XlsxReader();
        $spreadsheet2 = $reader2->load($this->outfile1);
        $sheet2 = $spreadsheet2->getActiveSheet();
        self::assertNull($spreadsheet2->getLegacyDrawing($sheet2));
        self::assertSame('row1', $sheet2->getCell('H1')->getValue());
        self::assertStringContainsString('Hello', $sheet2->getComment('F1')->getText()->getPlainText());
        self::assertStringContainsString('Goodbye', $sheet2->getComment('F2')->getText()->getPlainText());
        $spreadsheet2->disconnectWorksheets();
    }
}
