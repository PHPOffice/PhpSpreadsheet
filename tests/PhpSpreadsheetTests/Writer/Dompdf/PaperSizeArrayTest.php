<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Dompdf;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use PHPUnit\Framework\TestCase;

/**
 * Not clear that Dompdf will be Php8.4 compatible in time.
 * Run in separate process and add version test till it is ready.
 *
 * @runTestsInSeparateProcesses
 */
class PaperSizeArrayTest extends TestCase
{
    /** @var string */
    private $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testPaperSizeArray(): void
    {
        // Issue 1713 - array in PhpSpreadsheet is 2 elements,
        //   but in Dompdf it is 4 elements, first 2 are zero.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // TABLOID is a 2-element array in Writer/Pdf.php $paperSizes
        $size = PageSetup::PAPERSIZE_TABLOID;
        $sheet->getPageSetup()->setPaperSize($size);
        $sheet->setPrintGridlines(true);
        $sheet->getStyle('A7')->getAlignment()->setTextRotation(90);
        $sheet->setCellValue('A7', 'Lorem Ipsum');
        $writer = new Dompdf($spreadsheet);
        $this->outfile = File::temporaryFilename();
        if (PHP_VERSION_ID >= 80400) { // hopefully temporary
            @$writer->save($this->outfile);
        } else {
            $writer->save($this->outfile);
        }
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $contents = file_get_contents($this->outfile);
        self::assertNotFalse($contents);
        self::assertStringContainsString('/MediaBox [0.000 0.000 792.000 1224.000]', $contents);
    }

    public function testPaperSizeNotArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // LETTER is a string in Writer/Pdf.php $paperSizes
        $size = PageSetup::PAPERSIZE_LETTER;
        $sheet->getPageSetup()->setPaperSize($size);
        $sheet->setPrintGridlines(true);
        $sheet->getStyle('A7')->getAlignment()->setTextRotation(90);
        $sheet->setCellValue('A7', 'Lorem Ipsum');
        $writer = new Dompdf($spreadsheet);
        $this->outfile = File::temporaryFilename();
        if (PHP_VERSION_ID >= 80400) { // hopefully temporary
            @$writer->save($this->outfile);
        } else {
            $writer->save($this->outfile);
        }
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        $contents = file_get_contents($this->outfile);
        self::assertNotFalse($contents);
        self::assertStringContainsString('/MediaBox [0.000 0.000 612.000 792.000]', $contents);
    }
}
