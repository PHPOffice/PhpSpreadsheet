<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class AlignmentMiddleTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private string $outputFileName = '';

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->outputFileName !== '') {
            unlink($this->outputFileName);
            $this->outputFileName = '';
        }
    }

    public function testCenterWriteHtml(): void
    {
        // Html Writer changes vertical align center to middle
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Cell1');
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $writer = new Html($this->spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('vertical-align:middle', $html);
        self::assertStringNotContainsString('vertical-align:center', $html);
    }

    public function testCenterWriteXlsx(): void
    {
        // Xlsx Writer uses vertical align center unchanged
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Cell1');
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $this->outputFileName = File::temporaryFilename();
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->outputFileName);
        $zip = new ZipArchive();
        $zip->open($this->outputFileName);
        $html = $zip->getFromName('xl/styles.xml');
        $zip->close();
        self::assertStringContainsString('vertical="center"', $html);
        self::assertStringNotContainsString('vertical="middle"', $html);
    }

    public function testCenterWriteXlsx2(): void
    {
        // Xlsx Writer changes vertical align middle to center
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('Cell1');
        $sheet->getStyle('A1')
            ->getAlignment()
            ->setVertical('middle');
        $this->outputFileName = File::temporaryFilename();
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->outputFileName);
        $zip = new ZipArchive();
        $zip->open($this->outputFileName);
        $html = $zip->getFromName('xl/styles.xml');
        $zip->close();
        self::assertStringContainsString('vertical="center"', $html);
        self::assertStringNotContainsString('vertical="middle"', $html);
    }
}
