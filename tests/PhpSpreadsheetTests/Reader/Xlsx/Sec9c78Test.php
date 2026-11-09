<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Sec9c78Test extends TestCase
{
    public function test9c78(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('min col > max col');
        $infile = 'tests/data/Reader/XLSX/sec9c78.xlsx';
        $reader = new XlsxReader();
        $reader->load($infile);
    }

    public function testgfc9(): void
    {
        $infile = 'tests/data/Reader/XLSX/benign_min1_max16384.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEqualsWithDelta(10.0, $sheet->getColumnDimension('A')->getWidth(), 1e-8);
        // range runs to the last column, only its first column gets a dimension
        self::assertFalse($sheet->columnDimensionExists('XFD'));
        self::assertSame('A', $sheet->getHighestColumn());
        $spreadsheet->disconnectWorksheets();
    }
}
