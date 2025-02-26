<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class LoadSheetsOnlyTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private static string $testbook = 'tests/data/Reader/XLSX/HiddenSheet.xlsx';

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    public function testLoadSheet1Only(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        //$reader->setLoadSheetsOnly(['Sheet1']);
        $names = $reader->listWorksheetNames($filename);
        $reader->setLoadSheetsOnly([$names[0]]);
        $this->spreadsheet = $reader->load($filename);
        self::assertSame(1, $this->spreadsheet->getSheetCount());
        self::assertSame('Sheet1', $this->spreadsheet->getActiveSheet()->getTitle());
    }

    public function testLoadSheet2Only(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $reader->setLoadSheetsOnly(['Sheet2']);
        $this->spreadsheet = $reader->load($filename);
        self::assertSame(1, $this->spreadsheet->getSheetCount());
        self::assertSame('Sheet2', $this->spreadsheet->getActiveSheet()->getTitle());
    }

    public function testLoadNoSheet(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('You tried to set a sheet active by the out of bounds index');
        $filename = self::$testbook;
        $reader = new Xlsx();
        $reader->setLoadSheetsOnly(['Sheet3']);
        $reader->load($filename);
    }

    public function testLoadMultipleSheets(): void
    {
        $filename = 'tests/data/Reader/XLSX/threesheets.xlsx';
        $reader = new Xlsx();
        $reader->setLoadSheetsOnly(['Sheet3', 'Sheet1']);
        $spreadsheet = $this->spreadsheet = $reader->load($filename);
        self::assertSame(2, $spreadsheet->getSheetCount());
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet1');
        self::assertSame('First', $sheet->getCell('A1')->getValue());
        $sheet = $spreadsheet->getSheetByNameOrThrow('Sheet3');
        self::assertSame('Third', $sheet->getCell('A1')->getValue());
    }
}
