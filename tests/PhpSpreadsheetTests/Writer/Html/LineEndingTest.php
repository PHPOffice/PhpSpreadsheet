<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class LineEndingTest extends TestCase
{
    private int $expectedLines = 58;

    private Spreadsheet $spreadsheet;

    protected function tearDown(): void
    {
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function testDefault(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new HtmlWriter($this->spreadsheet);
        self::assertSame(PHP_EOL, $writer->getLineEnding());
        $html = $writer->generateHtmlAll();
        $count = substr_count($html, PHP_EOL);
        self::assertSame($this->expectedLines, $count);
    }

    public function testUnix(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new HtmlWriter($this->spreadsheet);
        $writer->setLineEnding("\n");
        $html = $writer->generateHtmlAll();
        $count = substr_count($html, "\n");
        self::assertSame($this->expectedLines, $count);
        $count = substr_count($html, "\r");
        self::assertSame(0, $count);
    }

    public function testWindows(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new HtmlWriter($this->spreadsheet);
        $writer->setLineEnding("\r\n");
        $html = $writer->generateHtmlAll();
        $count = substr_count($html, "\n");
        self::assertSame($this->expectedLines, $count);
        $count = substr_count($html, "\r");
        self::assertSame($this->expectedLines, $count);
        $count = substr_count($html, "\r\n");
        self::assertSame($this->expectedLines, $count);
    }

    public function testInvalidLineEnding(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Line ending must be');
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new HtmlWriter($this->spreadsheet);
        $writer->setLineEnding("\r");
    }
}
