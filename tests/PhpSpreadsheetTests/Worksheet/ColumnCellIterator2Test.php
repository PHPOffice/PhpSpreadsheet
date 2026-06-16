<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ColumnCellIterator2Test extends TestCase
{
    // Phpstan does not think RowCellIterator can return null
    private static function isCellNull(?Cell $item): bool
    {
        return $item === null;
    }

    #[DataProvider('providerExistingCell')]
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
            if (!self::isCellNull($cell)) {
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
            [null, 'B1', 'B8'],
            [false, 'B1', 'B8'],
            [true, 'B2', 'B6'],
        ];
    }

    #[DataProvider('providerEmptyColumn')]
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
        self::assertSame($expectedResult, $numCells);
    }

    public static function providerEmptyColumn(): array
    {
        return [
            [null, 6], // Default behaviour
            [false, 6],
            [true, 0],
        ];
    }

    #[DataProvider('providerNullOrCreate')]
    public function testNullOrCreateOption(?bool $existingBehaviour, int $expectedCreatedResult, mixed $expectedNullResult): void
    {
        if (!is_int($expectedNullResult)) {
            self::fail('unexpected unused arg');
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $iterator = new ColumnCellIterator($sheet, 'F');
        if (isset($existingBehaviour)) {
            $iterator->setIfNotExists($existingBehaviour);
        }
        $notExistsBehaviour = $iterator->getIfNotExists();
        self::assertSame($expectedCreatedResult > 0, $notExistsBehaviour);
    }

    #[DataProvider('providerNullOrCreate')]
    public function testNullOrCreate(?bool $existing, int $expectedCreatedResult, int $expectedNullResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('B2')->setValue('cellb2');
        $sheet->getCell('F4')->setValue('cellf2');

        $iterator = new ColumnCellIterator($sheet, 'F');
        if (isset($existing)) {
            $iterator->setIfNotExists($existing);
        }
        $numCreatedCells = $numEmptyCells = 0;
        foreach ($iterator as $cell) {
            $numCreatedCells += (int) (!self::isCellNull($cell) && $cell->getValue() === null);
            $numEmptyCells += (int) (self::isCellNull($cell));
        }
        self::assertSame($expectedCreatedResult, $numCreatedCells);
        self::assertSame($expectedNullResult, $numEmptyCells);
    }

    public static function providerNullOrCreate(): array
    {
        return [
            [null, 3, 0], // Default behaviour
            [CellIterator::IF_NOT_EXISTS_CREATE_NEW, 3, 0],
            [CellIterator::IF_NOT_EXISTS_RETURN_NULL, 0, 3],
        ];
    }
}
