<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PHPUnit\Framework\TestCase;

class ColumnCellIterator2Test extends TestCase
{
    /**
     * @dataProvider providerExistingCell
     */
    public function testEndRange(?bool $existing, string $expectedResultFirst, string $expectedResultLast): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue('cellb2');
        $sheet->getCell('B6')->setValue('cellb6');

        $iterator = new ColumnCellIterator($sheet, 'B', 1, 8);
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
            [null, 'B1', 'B8'],
            [false, 'B1', 'B8'],
            [true, 'B2', 'B6'],
        ];
    }

    /**
     * @dataProvider providerEmptyColumn
     */
    public function testEmptyColumn(?bool $existing, int $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue('cellb2');
        $sheet->getCell('B6')->setValue('cellb6');

        $iterator = new ColumnCellIterator($sheet, 'C');
        if (isset($existing)) {
            $iterator->setIterateOnlyExistingCells($existing);
        }
        $numCells = 0;
        foreach ($iterator as $cell) {
            ++$numCells;
        }
        self::assertEquals($expectedResult, $numCells);
    }

    public function providerEmptyColumn(): array
    {
        return [
            [null, 6],
            [false, 6],
            [true, 0],
        ];
    }
}
