<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelperTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testColumnSort(): void
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        usort($columnBase, [ReferenceHelper::class, 'columnSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testColumnReverseSort(): void
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        $columnExpectedResult = array_reverse($columnExpectedResult);
        usort($columnBase, [ReferenceHelper::class, 'columnReverseSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testCellSort(): void
    {
        $cellBase = $columnExpectedResult = [
            'A1', 'B1', 'AZB1',
            'BBB1', 'BB2', 'BAB2',
            'BZA2', 'Z3', 'AZA3',
            'BZB3', 'AB5', 'AZ6',
            'ABZ7', 'BA9', 'BZ9',
            'AAA9', 'AAZ9', 'BA10',
            'BZZ10', 'ZA11', 'AAB11',
            'BBZ29', 'BAA32', 'ZZ43',
            'AZZ43', 'BAZ67', 'ZB78',
            'ABA121', 'ABB289', 'BBA544',
        ];
        shuffle($cellBase);
        usort($cellBase, [ReferenceHelper::class, 'cellSort']);
        foreach ($cellBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testCellReverseSort(): void
    {
        $cellBase = $columnExpectedResult = [
            'BBA544', 'ABB289', 'ABA121',
            'ZB78', 'BAZ67', 'AZZ43',
            'ZZ43', 'BAA32', 'BBZ29',
            'AAB11', 'ZA11', 'BZZ10',
            'BA10', 'AAZ9', 'AAA9',
            'BZ9', 'BA9', 'ABZ7',
            'AZ6', 'AB5', 'BZB3',
            'AZA3', 'Z3', 'BZA2',
            'BAB2', 'BB2', 'BBB1',
            'AZB1', 'B1', 'A1',
        ];
        shuffle($cellBase);
        usort($cellBase, [ReferenceHelper::class, 'cellReverseSort']);
        foreach ($cellBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    /**
     * @dataProvider providerFormulaUpdates
     */
    public function testUpdateFormula(string $formula, int $insertRows, int $insertColumns, string $worksheet, string $expectedResult): void
    {
        $referenceHelper = ReferenceHelper::getInstance();

        $result = $referenceHelper->updateFormulaReferences($formula, 'A1', $insertRows, $insertColumns, $worksheet);

        self::assertSame($expectedResult, $result);
    }

    public function providerFormulaUpdates(): array
    {
        return require 'tests/data/ReferenceHelperFormulaUpdates.php';
    }

    /**
     * @dataProvider providerMultipleWorksheetFormulaUpdates
     */
    public function testUpdateFormulaForMultipleWorksheets(string $formula, int $insertRows, int $insertColumns, string $expectedResult): void
    {
        $referenceHelper = ReferenceHelper::getInstance();

        $result = $referenceHelper->updateFormulaReferencesAnyWorksheet($formula, $insertRows, $insertColumns);

        self::assertSame($expectedResult, $result);
    }

    public function providerMultipleWorksheetFormulaUpdates(): array
    {
        return require 'tests/data/ReferenceHelperFormulaUpdatesMultipleSheet.php';
    }

    public function testInsertNewBeforeRetainDataType(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValueExplicit('+1', DataType::TYPE_STRING);
        $oldDataType = $cell->getDataType();
        $oldValue = $cell->getValue();

        $sheet->insertNewRowBefore(1);
        $newCell = $sheet->getCell('A2');
        $newDataType = $newCell->getDataType();
        $newValue = $newCell->getValue();

        self::assertSame($oldValue, $newValue);
        self::assertSame($oldDataType, $newDataType);
    }

    public function testRemoveColumnShiftsCorrectColumnValueIntoRemovedColumnCoordinates(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', null],
        ]);

        $cells = $sheet->toArray();
        self::assertSame('a1', $cells[0][0]);
        self::assertSame('b1', $cells[0][1]);
        self::assertSame('c1', $cells[0][2]);
        self::assertSame('a2', $cells[1][0]);
        self::assertSame('b2', $cells[1][1]);
        self::assertNull($cells[1][2]);

        $sheet->removeColumn('B');

        $cells = $sheet->toArray();
        self::assertSame('a1', $cells[0][0]);
        self::assertSame('c1', $cells[0][1]);
        self::assertArrayNotHasKey(2, $cells[0]);
        self::assertSame('a2', $cells[1][0]);
        self::assertNull($cells[1][1]);
        self::assertArrayNotHasKey(2, $cells[1]);
    }
}
