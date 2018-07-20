<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class XlsTest extends TestCase
{
    /**
     * Test load Xls file without cell reference.
     */
    public function testLoadXlsWithoutCellReference()
    {
        $filename = './data/Reader/Xls/without_cell_reference.xls';
        $reader = new Xls();
        $reader->load($filename);
    }

    /**
     * Test sum data in xls files.
     */
    public function testSumData()
    {
        $filename = './data/Reader/Xls/sum_data.xls';
        $reader = new Xls();
        $spreadSheet = $reader->load($filename);

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
            ]
        ];

        foreach ($tests as $sheetName => $testsForSheet) {
            $sheet = $spreadSheet->getSheetByName($sheetName);

            foreach ($testsForSheet as $cellCoordinate => $result) {
                $calculatedValue = $sheet->getCell($cellCoordinate)->getCalculatedValue();

                if ($calculatedValue != $result) {
                    if (empty($calculatedValue) === true) {
                        $calculatedValue = 'empty';
                    }

                    throw new \Exception($cellCoordinate . ' != ' . $result . ' - Result in sheet with name ' . $sheetName . ' is ' . $calculatedValue . '.');
                }
            }
        }
    }
}
