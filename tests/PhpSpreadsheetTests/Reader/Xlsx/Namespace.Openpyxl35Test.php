<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamespaceOpenpyxl35Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/namespaces.openpyxl35.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<s:workbook ', $data);
        }
    }

    public function testInfo(): void
    {
        $reader = new Xlsx();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info0 = $workSheetInfo[0];
        self::assertEquals('Shofar 5781', $info0['worksheetName']);
        self::assertEquals('D', $info0['lastColumnLetter']);
        self::assertEquals(3, $info0['lastColumnIndex']);
        self::assertEquals(30, $info0['totalRows']);
        self::assertEquals(4, $info0['totalColumns']);
    }

    public function testSheetNames(): void
    {
        $reader = new Xlsx();
        $worksheetNames = $reader->listWorksheetNames(self::$testbook);
        self::assertEquals(['Shofar 5781', 'Shofar 5782', 'Shofar 5783'], $worksheetNames);
    }

    public function testActive(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Shofar 5781', $sheet->getTitle());
        self::assertSame('A2', $sheet->getFreezePane());
        self::assertSame('A2', $sheet->getTopLeftCell());
        self::assertSame('D2', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }

    private static function getCellValue(Worksheet $sheet, string $cell): string
    {
        $result = $sheet->getCell($cell)->getValue();

        return (string) $result;
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Shofar 5781', $sheet->getTitle());
        $expectedArray = [
            'Shofar 5781' => [
                'A1' => 'Weekday',
                'B6' => 'August 13',
                'A14' => 'Saturday',
                'C14' => 'Elul 13',
                'D30' => 'N/A',
                'B30' => 'September 6',
            ],
            'Shofar 5782' => [
                'C1' => 'Jewish Date',
                'B6' => 'September 1',
                'A14' => 'Friday',
                'C14' => 'Elul 13',
                'D28' => '',
                'B30' => 'September 25',
            ],
            'Shofar 5783' => [
                'B1' => 'Civil Date',
                'B6' => 'August 22',
                'A14' => 'Wednesday',
                'C14' => 'Elul 13',
                'D30' => 'N/A',
                'B30' => 'September 15',
            ],
        ];
        foreach ($expectedArray as $sheetName => $array1) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet === null) {
                self::fail("Unable to find sheet $sheetName");
            } else {
                foreach ($array1 as $key => $value) {
                    self::assertSame($value, self::getCellValue($sheet, $key), "error in sheet $sheetName cell $key");
                }
            }
        }
        $spreadsheet->disconnectWorksheets();
    }
}
