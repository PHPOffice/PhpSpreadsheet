<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue4477Test extends TestCase
{
    private string $tempfile = '';

    protected function tearDown(): void
    {
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function testDataonlyNoPrinter(): void
    {
        // Need to ignore printer settings when Read Dataonly
        $infile = 'tests/data/Reader/XLSX/issue.4477.disclaimer.xlsx';
        $zip = new ZipArchive();
        if ($zip->open($infile) !== true) {
            self::fail("failed to open $infile");
        }
        $num = $zip->numFiles;
        $foundPrinter = $foundWorksheet = false;
        for ($i = 0; $i < $num; ++$i) {
            $filename = (string) $zip->getNameIndex($i);
            if (str_contains($filename, 'printer')) {
                $foundPrinter = true;
            } elseif ($filename === 'xl/worksheets/sheet1.xml') {
                $foundWorksheet = true;
            }
        }
        $zip->close();
        self::assertTrue($foundPrinter);
        self::assertTrue($foundWorksheet);

        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($infile);
        $writer = new XlsxWriter($spreadsheet);
        $this->tempfile = File::temporaryFileName();
        $writer->save($this->tempfile);
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        if ($zip->open($this->tempfile) !== true) {
            self::fail("failed to open {$infile}");
        }
        $num = $zip->numFiles;
        $foundPrinter = $foundWorksheet = false;
        for ($i = 0; $i < $num; ++$i) {
            $filename = (string) $zip->getNameIndex($i);
            if (str_contains($filename, 'printer')) {
                $foundPrinter = true;
            } elseif ($filename === 'xl/worksheets/sheet1.xml') {
                $foundWorksheet = true;
            }
        }
        $zip->close();
        self::assertFalse($foundPrinter);
        self::assertTrue($foundWorksheet);
    }
}
