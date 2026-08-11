<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PHPUnit\Framework\TestCase;

class Pr607Test extends TestCase
{
    /**
     * Test file with cell range expressed in unexpected manner.
     */
    public static function testSumData(): void
    {
        $filename = 'tests/data/Reader/XLS/pr607.sum_data.xls';
        $reader = new XlsReader();
        $spreadsheet = $reader->load($filename);

        $tests = [
            'Test' => [
                'A1' => 1,
                'A2' => 2,
                'A3' => 3,
                'A4' => 4,
                'A5' => 5,
                'A6' => 6,
                'A7' => 7,
                'A8' => 8,
                'A9' => 9,
                'A10' => 10,

                'B1' => 3,
                'B2' => 4,
                'B3' => 5,
                'B4' => 6,
                'B5' => 7,
                'B6' => 8,
                'B7' => 9,
                'B8' => 10,
                'B9' => 11,
                'B10' => 12,

                'C1' => 4,
                'C2' => 6,
                'C3' => 8,
                'C4' => 10,
                'C5' => 12,
                'C6' => 14,
                'C7' => 16,
                'C8' => 18,
                'C9' => 20,
                'C10' => 22,
            ],
        ];

        foreach ($tests as $sheetName => $testsForSheet) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            self::assertNotNull($sheet);

            foreach ($testsForSheet as $cellCoordinate => $result) {
                $calculatedValue = $sheet->getCell($cellCoordinate)->getCalculatedValue();
                self::assertSame($result, $calculatedValue, "cell $cellCoordinate");
            }
        }
        $spreadsheet->disconnectWorksheets();
    }
}
