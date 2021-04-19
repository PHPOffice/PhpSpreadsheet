<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PHPUnit\Framework\TestCase;

class RowCellIterator2Test extends TestCase
{
    /**
     * @dataProvider providerExistingCell
     */
    public function testEndRangeTrue(?bool $existing, string $expectedResultFirst, string $expectedResultLast): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('C2')->setValue('cellb2');
        $sheet->getCell('F2')->setValue('cellf2');

        $iterator = new RowCellIterator($sheet, 2, 'B', 'H');
        if (isset($existing)) {
            $iterator->setIterateOnlyExistingCells($existing);
        }
        $lastCoordinate = '';
        $firstCoordinate = '';
        foreach ($iterator as $cell) {
            if ($cell !== null) {
                $lastCoordinate = $cell->getCoordinate();
                if (!$firstCoordinate) {
                    $firstCoordinate = $lastCoordinate;
                }
            }
        }
        self::assertEquals($expectedResultFirst, $firstCoordinate);
        self::assertEquals($expectedResultLast, $lastCoordinate);
    }

    public function providerExistingCell(): array
    {
        return [
            [null, 'B2', 'H2'],
            [false, 'B2', 'H2'],
            [true, 'C2', 'F2'],
        ];
    }

    /**
     * @dataProvider providerEmptyRow
     */
    public function testEmptyRow(?bool $existing, int $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue('cellb2');
        $sheet->getCell('F2')->setValue('cellf2');

        $iterator = new RowCellIterator($sheet, '3');
        if (isset($existing)) {
            $iterator->setIterateOnlyExistingCells($existing);
        }
        $numCells = 0;
        foreach ($iterator as $cell) {
            ++$numCells;
        }
        self::assertEquals($expectedResult, $numCells);
    }

    public function providerEmptyRow(): array
    {
        return [
            [null, 6],
            [false, 6],
            [true, 0],
        ];
    }
}
