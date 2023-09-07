<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
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
        self::assertSame($expectedResultFirst, $firstCoordinate);
        self::assertSame($expectedResultLast, $lastCoordinate);
    }

    public static function providerExistingCell(): array
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

        $iterator = new RowCellIterator($sheet, 3);
        if (isset($existing)) {
            $iterator->setIterateOnlyExistingCells($existing);
        }
        $numCells = 0;
        foreach ($iterator as $cell) {
            ++$numCells;
        }
        self::assertSame($expectedResult, $numCells);
    }

    public static function providerEmptyRow(): array
    {
        return [
            [null, 6], // Default behaviour
            [false, 6],
            [true, 0],
        ];
    }

    /**
     * @dataProvider providerNullOrCreate
     */
    public function testNullOrCreateOption(?bool $existingBehaviour, int $expectedCreatedResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $iterator = new RowCellIterator($sheet, 2);
        if (isset($existingBehaviour)) {
            $iterator->setIfNotExists($existingBehaviour);
        }
        $notExistsBehaviour = $iterator->getIfNotExists();
        self::assertSame($expectedCreatedResult > 0, $notExistsBehaviour);
    }

    /**
     * @dataProvider providerNullOrCreate
     */
    public function testNullOrCreate(?bool $existing, int $expectedCreatedResult, int $expectedNullResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue('cellb2');
        $sheet->getCell('F2')->setValue('cellf2');

        $iterator = new RowCellIterator($sheet, 2);
        if (isset($existing)) {
            $iterator->setIfNotExists($existing);
        }
        $numCreatedCells = $numEmptyCells = 0;
        foreach ($iterator as $cell) {
            $numCreatedCells += (int) ($cell !== null && $cell->getValue() === null);
            // @phpstan-ignore-next-line
            $numEmptyCells += (int) ($cell === null);
        }
        self::assertSame($expectedCreatedResult, $numCreatedCells);
        self::assertSame($expectedNullResult, $numEmptyCells);
    }

    public static function providerNullOrCreate(): array
    {
        return [
            [null, 4, 0], // Default behaviour
            [CellIterator::IF_NOT_EXISTS_CREATE_NEW, 4, 0],
            [CellIterator::IF_NOT_EXISTS_RETURN_NULL, 0, 4],
        ];
    }
}
