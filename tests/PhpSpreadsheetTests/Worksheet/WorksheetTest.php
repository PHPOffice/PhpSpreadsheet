<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

    public function setTitleInvalidProvider(): array
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet title.'],
            ['invalid*title', 'Invalid character found in sheet title'],
        ];
    }

    /**
     * @param string $title
     * @param string $expectMessage
     * @dataProvider setTitleInvalidProvider
     */
    public function testSetTitleInvalid($title, $expectMessage): void
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

    public function setCodeNameInvalidProvider(): array
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet code name.'],
            ['invalid*code*name', 'Invalid character found in sheet code name'],
        ];
    }

    /**
     * @param string $codeName
     * @param string $expectMessage
     * @dataProvider setCodeNameInvalidProvider
     */
    public function testSetCodeNameInvalid($codeName, $expectMessage): void
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

    public function extractSheetTitleProvider(): array
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
     * @param string $range
     * @param string $expectTitle
     * @param string $expectCell
     * @param string $expectCell2
     * @dataProvider extractSheetTitleProvider
     */
    public function testExtractSheetTitle($range, $expectTitle, $expectCell, $expectCell2): void
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

    public function removeColumnProvider(): array
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

    public function removeRowsProvider(): array
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
}
