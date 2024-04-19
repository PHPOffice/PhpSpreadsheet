<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class WorksheetTest extends TestCase
{
    public function testSetTitle(): void
    {
        $testTitle = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setTitle($testTitle);
        self::assertSame($testTitle, $worksheet->getTitle());
    }

    public static function setTitleInvalidProvider(): array
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet title.'],
            ['invalid*title', 'Invalid character found in sheet title'],
        ];
    }

    /**
     * @dataProvider setTitleInvalidProvider
     */
    public function testSetTitleInvalid(string $title, string $expectMessage): void
    {
        // First, test setting title with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setTitle($title, true, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setTitle($title);
    }

    public function testSetTitleDuplicate(): void
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique title -- should be unchanged
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Test Title');
        self::assertSame('Test Title', $sheet->getTitle());

        // Set duplicate title -- should have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setTitle('Test Title');
        self::assertSame('Test Title 1', $sheet->getTitle());

        // Set duplicate title with validation disabled -- should be unchanged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setTitle('Test Title', true, false);
        self::assertSame('Test Title', $sheet->getTitle());
    }

    public function testSetCodeName(): void
    {
        $testCodeName = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setCodeName($testCodeName);
        self::assertSame($testCodeName, $worksheet->getCodeName());
    }

    public static function setCodeNameInvalidProvider(): array
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet code name.'],
            ['invalid*code*name', 'Invalid character found in sheet code name'],
            ['', 'Sheet code name cannot be empty'],
        ];
    }

    /**
     * @dataProvider setCodeNameInvalidProvider
     */
    public function testSetCodeNameInvalid(string $codeName, string $expectMessage): void
    {
        // First, test setting code name with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setCodeName($codeName, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setCodeName($codeName);
    }

    public function testSetCodeNameDuplicate(): void
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique code name -- should be massaged to Snake_Case
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setCodeName('Test Code Name');
        self::assertSame('Test_Code_Name', $sheet->getCodeName());

        // Set duplicate code name -- should be massaged and have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setCodeName('Test Code Name');
        self::assertSame('Test_Code_Name_1', $sheet->getCodeName());

        // Set duplicate code name with validation disabled -- should be unchanged, and unmassaged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setCodeName('Test Code Name', false);
        self::assertSame('Test Code Name', $sheet->getCodeName());
    }

    public function testFreezePaneSelectedCell(): void
    {
        $worksheet = new Worksheet();
        $worksheet->freezePane('B2');
        self::assertSame('B2', $worksheet->getTopLeftCell());
    }

    public static function extractSheetTitleProvider(): array
    {
        return [
            ['B2', '', '', 'B2'],
            ['testTitle!B2', 'testTitle', 'B2', 'B2'],
            ['test!Title!B2', 'test!Title', 'B2', 'B2'],
            ['test Title!B2', 'test Title', 'B2', 'B2'],
            ['test!Title!B2', 'test!Title', 'B2', 'B2'],
            ["'testSheet 1'!A3", "'testSheet 1'", 'A3', 'A3'],
            ["'testSheet1'!A2", "'testSheet1'", 'A2', 'A2'],
            ["'testSheet 2'!A1", "'testSheet 2'", 'A1', 'A1'],
        ];
    }

    /**
     * @dataProvider extractSheetTitleProvider
     */
    public function testExtractSheetTitle(string $range, string $expectTitle, string $expectCell, string $expectCell2): void
    {
        // only cell reference
        self::assertSame($expectCell, Worksheet::extractSheetTitle($range));
        // with title in array
        $arRange = Worksheet::extractSheetTitle($range, true);
        self::assertSame($expectTitle, $arRange[0]);
        self::assertSame($expectCell2, $arRange[1]);
    }

    /**
     * Fix https://github.com/PHPOffice/PhpSpreadsheet/issues/868 when cells are not removed correctly
     * on row deletion.
     */
    public function testRemoveCellsCorrectlyWhenRemovingRow(): void
    {
        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->getCell('A2')->setValue('A2');
        $worksheet->getCell('C1')->setValue('C1');
        $worksheet->removeRow(1);
        self::assertEquals(
            'A2',
            $worksheet->getCell('A1')->getValue()
        );
        self::assertNull(
            $worksheet->getCell('C1')->getValue()
        );
    }

    public static function removeColumnProvider(): array
    {
        return [
            'Remove first column' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'A',
                1,
                [
                    ['B1', 'C1'],
                    ['B2', 'C2'],
                ],
                'B',
            ],
            'Remove middle column' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'B',
                1,
                [
                    ['A1', 'C1'],
                    ['A2', 'C2'],
                ],
                'B',
            ],
            'Remove last column' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'C',
                1,
                [
                    ['A1', 'B1'],
                    ['A2', 'B2'],
                ],
                'B',
            ],
            'Remove a column out of range' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'D',
                1,
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'C',
            ],
            'Remove multiple columns' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                ],
                'B',
                5,
                [
                    ['A1'],
                    ['A2'],
                ],
                'A',
            ],
            'Data includes nulls' => [
                [
                    ['A1', 'B1', 'C1', 'D1', 'E1'],
                    [null, 'B2', 'C2', 'D2', 'E2'],
                    ['A3', null, 'C3', 'D3', 'E3'],
                    ['A4', 'B4', null, 'D4', 'E4'],
                    ['A5', 'B5', 'C5', null, 'E5'],
                    ['A6', 'B6', 'C6', 'D6', null],
                ],
                'B',
                2,
                [
                    ['A1', 'D1', 'E1'],
                    [null, 'D2', 'E2'],
                    ['A3', 'D3', 'E3'],
                    ['A4', 'D4', 'E4'],
                    ['A5', null, 'E5'],
                    ['A6', 'D6', null],
                ],
                'C',
            ],
        ];
    }

    /**
     * @dataProvider removeColumnProvider
     */
    public function testRemoveColumn(
        array $initialData,
        string $columnToBeRemoved,
        int $columnsToBeRemoved,
        array $expectedData,
        string $expectedHighestColumn
    ): void {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($initialData);

        $worksheet->removeColumn($columnToBeRemoved, $columnsToBeRemoved);

        self::assertSame($expectedHighestColumn, $worksheet->getHighestColumn());
        self::assertSame($expectedData, $worksheet->toArray());
    }

    public static function removeRowsProvider(): array
    {
        return [
            'Remove all rows except first one' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                2,
                3,
                [
                    ['A1', 'B1', 'C1'],
                ],
                1,
            ],
            'Remove all rows except last one' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                1,
                3,
                [
                    ['A4', 'B4', 'C4'],
                ],
                1,
            ],
            'Remove last row' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                4,
                1,
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                ],
                3,
            ],
            'Remove first row' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                1,
                1,
                [
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                3,
            ],
            'Remove all rows except first and last' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                2,
                2,
                [
                    ['A1', 'B1', 'C1'],
                    ['A4', 'B4', 'C4'],
                ],
                2,
            ],
            'Remove non existing rows' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                2,
                10,
                [
                    ['A1', 'B1', 'C1'],
                ],
                1,
            ],
            'Remove only non existing rows' => [
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                5,
                10,
                [
                    ['A1', 'B1', 'C1'],
                    ['A2', 'B2', 'C2'],
                    ['A3', 'B3', 'C3'],
                    ['A4', 'B4', 'C4'],
                ],
                4,
            ],
            'Data includes nulls' => [
                [
                    ['A1', 'B1', 'C1', 'D1', 'E1'],
                    [null, 'B2', 'C2', 'D2', 'E2'],
                    ['A3', null, 'C3', 'D3', 'E3'],
                    ['A4', 'B4', null, 'D4', 'E4'],
                    ['A5', 'B5', 'C5', null, 'E5'],
                    ['A6', 'B6', 'C6', 'D6', null],
                ],
                1,
                2,
                [
                    ['A3', null, 'C3', 'D3', 'E3'],
                    ['A4', 'B4', null, 'D4', 'E4'],
                    ['A5', 'B5', 'C5', null, 'E5'],
                    ['A6', 'B6', 'C6', 'D6', null],
                ],
                4,
            ],
        ];
    }

    /**
     * @dataProvider removeRowsProvider
     */
    public function testRemoveRows(
        array $initialData,
        int $rowToRemove,
        int $rowsQtyToRemove,
        array $expectedData,
        int $expectedHighestRow
    ): void {
        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->fromArray($initialData);

        $worksheet->removeRow($rowToRemove, $rowsQtyToRemove);

        self::assertSame($expectedData, $worksheet->toArray());
        self::assertSame($expectedHighestRow, $worksheet->getHighestRow());
    }

    private static function getPopulatedSheetForEmptyRowTest(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', 'Hello World', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B3', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('B4', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B5', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C5', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B6', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C6', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B7', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C7', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B8', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C8', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D8', 'PHP', DataType::TYPE_STRING);

        return $sheet;
    }

    private static function getPopulatedSheetForEmptyColumnTest(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', 'Hello World', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('D2', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('E3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('F3', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G2', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G3', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('H2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('H3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('H4', 'PHP', DataType::TYPE_STRING);

        return $sheet;
    }

    /**
     * @dataProvider emptyRowProvider
     */
    public function testIsEmptyRow(int $rowId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheetForEmptyRowTest($spreadsheet);

        $isEmpty = $sheet->isEmptyRow($rowId, CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL | CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);

        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyRowProvider(): array
    {
        return [
            [1, false],
            [2, true],
            [3, true],
            [4, true],
            [5, true],
            [6, false],
            [7, false],
            [8, false],
            [9, true],
        ];
    }

    /**
     * @dataProvider emptyColumnProvider
     */
    public function testIsEmptyColumn(string $columnId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheetForEmptyColumnTest($spreadsheet);

        $isEmpty = $sheet->isEmptyColumn($columnId, CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL | CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);

        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyColumnProvider(): array
    {
        return [
            ['A', false],
            ['B', true],
            ['C', true],
            ['D', true],
            ['E', true],
            ['F', false],
            ['G', false],
            ['H', false],
            ['I', true],
        ];
    }

    public function testGetTableNames(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load('tests/data/Worksheet/Table/TableFormulae.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $tables = $worksheet->getTableNames();
        self::assertSame(['DeptSales'], $tables);
    }

    public function testGetTableByName(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load('tests/data/Worksheet/Table/TableFormulae.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $table = $worksheet->getTableByName('Non-existent Table');
        self::assertNull($table);

        $table = $worksheet->getTableByName('DeptSales');
        self::assertInstanceOf(Table::class, $table);
    }

    /**
     * @dataProvider toArrayHiddenRowsProvider
     */
    public function testHiddenRows(
        array $initialData,
        array $hiddenRows,
        array $expectedData
    ): void {
        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->fromArray($initialData);

        foreach ($hiddenRows as $hiddenRow) {
            $worksheet->getRowDimension($hiddenRow)->setVisible(false);
        }

        self::assertSame($expectedData, $worksheet->toArray(null, false, false, true, true));
    }

    public static function toArrayHiddenRowsProvider(): array
    {
        return [
            [
                [[1], [2], [3], [4], [5], [6]],
                [2, 3, 5],
                [1 => ['A' => 1], 4 => ['A' => 4], 6 => ['A' => 6]],
            ],
            [
                [[1], [2], [3], [4], [5], [6]],
                [1, 3, 6],
                [2 => ['A' => 2], 4 => ['A' => 4], 5 => ['A' => 5]],
            ],
        ];
    }

    /**
     * @dataProvider toArrayHiddenColumnsProvider
     */
    public function testHiddenColumns(
        array $initialData,
        array $hiddenColumns,
        array $expectedData
    ): void {
        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->fromArray($initialData);

        foreach ($hiddenColumns as $hiddenColumn) {
            $worksheet->getColumnDimension($hiddenColumn)->setVisible(false);
        }

        self::assertSame($expectedData, $worksheet->toArray(null, false, false, true, true));
    }

    public static function toArrayHiddenColumnsProvider(): array
    {
        return [
            [
                ['A', 'B', 'C', 'D', 'E', 'F'],
                ['B', 'C', 'E'],
                [1 => ['A' => 'A', 'D' => 'D', 'F' => 'F']],
            ],
            [
                ['A', 'B', 'C', 'D', 'E', 'F'],
                ['A', 'C', 'F'],
                [1 => ['B' => 'B', 'D' => 'D', 'E' => 'E']],
            ],
        ];
    }

    /**
     * @dataProvider rangeToArrayProvider
     */
    public function testRangeToArrayWithCellRangeObject(array $expected, string $fromCell, string $toCell): void
    {
        $initialData = array_chunk(range('A', 'Y'), 5);

        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->fromArray($initialData);

        $cellRange = new CellRange(new CellAddress($fromCell), new CellAddress($toCell));

        self::assertSame($expected, $worksheet->rangeToArray((string) $cellRange));
    }

    public static function rangeToArrayProvider(): array
    {
        return [
            [
                [['A', 'B'], ['F', 'G']],
                'A1', 'B2',
            ],
            [
                [['G', 'H', 'I'], ['L', 'M', 'N'], ['Q', 'R', 'S']],
                'B2', 'D4',
            ],
        ];
    }
}
