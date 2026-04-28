<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class ZipReadCacheTest extends TestCase
{
    private static string $testFile = 'tests/data/Reader/XLSX/Zip-Linux-Directory-Separator.xlsx';

    public function testCachedReadReturnsCorrectData(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testFile);

        $cellValue = $spreadsheet->getActiveSheet()->getCell('A1')->getValue();
        self::assertSame('Key ID', $cellValue);

        $spreadsheet->disconnectWorksheets();
    }

    public function testRepeatedLoadProducesSameResults(): void
    {
        $reader = new Xlsx();

        // First load
        $spreadsheet1 = $reader->load(self::$testFile);
        $value1 = $spreadsheet1->getActiveSheet()->getCell('A1')->getValue();
        $sheetCount1 = $spreadsheet1->getSheetCount();
        $sheetName1 = $spreadsheet1->getActiveSheet()->getTitle();

        // Second load on the same reader instance — cache should be cleared
        $spreadsheet2 = $reader->load(self::$testFile);
        $value2 = $spreadsheet2->getActiveSheet()->getCell('A1')->getValue();
        $sheetCount2 = $spreadsheet2->getSheetCount();
        $sheetName2 = $spreadsheet2->getActiveSheet()->getTitle();

        self::assertSame($value1, $value2);
        self::assertSame($sheetCount1, $sheetCount2);
        self::assertSame($sheetName1, $sheetName2);

        $spreadsheet1->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }

    public function testCacheClearedBetweenDifferentFiles(): void
    {
        $reader = new Xlsx();

        // Load first file
        $spreadsheet1 = $reader->load(self::$testFile);
        $value1 = $spreadsheet1->getActiveSheet()->getCell('A1')->getValue();
        self::assertSame('Key ID', $value1);

        // Load a different file on the same reader
        $secondFile = 'tests/data/Reader/XLSX/stylesTest.xlsx';
        $spreadsheet2 = $reader->load($secondFile);
        $sheetCount = $spreadsheet2->getSheetCount();
        self::assertGreaterThan(0, $sheetCount);

        // Values should NOT bleed between loads
        self::assertNotEmpty($spreadsheet2->getActiveSheet()->getTitle());

        $spreadsheet1->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }

    public function testListWorksheetNamesWorksWithCache(): void
    {
        $reader = new Xlsx();

        $names1 = $reader->listWorksheetNames(self::$testFile);
        $names2 = $reader->listWorksheetNames(self::$testFile);

        self::assertSame($names1, $names2);
        self::assertCount(1, $names1);
        self::assertSame('Sheet', $names1[0]);
    }

    public function testPerformanceConsistency(): void
    {
        $reader = new Xlsx();

        // First read
        $spreadsheet1 = $reader->load(self::$testFile);
        $data1 = [];
        foreach ($spreadsheet1->getActiveSheet()->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data1[] = $rowData;
        }

        // Second read on fresh reader (to confirm results are identical)
        $reader2 = new Xlsx();
        $spreadsheet2 = $reader2->load(self::$testFile);
        $data2 = [];
        foreach ($spreadsheet2->getActiveSheet()->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data2[] = $rowData;
        }

        self::assertSame($data1, $data2);

        $spreadsheet1->disconnectWorksheets();
        $spreadsheet2->disconnectWorksheets();
    }
}
