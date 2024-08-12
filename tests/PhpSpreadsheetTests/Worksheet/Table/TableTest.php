<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TableTest extends SetupTeardown
{
    private const INITIAL_RANGE = 'H2:O256';

    public function testToString(): void
    {
        $expectedResult = self::INITIAL_RANGE;
        $table = new Table(self::INITIAL_RANGE);

        //  magic __toString should return the active table range
        $result = (string) $table;
        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider validTableNamesProvider
     */
    public function testValidTableNames(string $name, string $expected): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $result = $table->setName($name);
        self::assertInstanceOf(Table::class, $result);
        self::assertEquals($expected, $table->getName());
    }

    public static function validTableNamesProvider(): array
    {
        return [
            ['', ''],
            ['Table_1', 'Table_1'],
            ['_table_2', '_table_2'],
            ['\table_3', '\table_3'],
            ["	Table_4 \n", 'Table_4'],
            ['table.5', 'table.5'],
            ['தமிழ்', 'தமிழ்'], // UTF-8 letters with combined character
        ];
    }

    /**
     * @dataProvider invalidTableNamesProvider
     */
    public function testInvalidTableNames(string $name): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $this->expectException(PhpSpreadsheetException::class);

        $table->setName($name);
    }

    public static function invalidTableNamesProvider(): array
    {
        return [
            ['C'],
            ['c'],
            ['R'],
            ['r'],
            ['Z100'],
            ['Z$100'],
            ['R1C1'],
            ['R1C'],
            ['R11C11'],
            ['123'],
            ['=Table'],
            ['Name/Slash'],
            ['ிக'], // starting with UTF-8 combined character
            [bin2hex(random_bytes(255))], // random string with length greater than 255
        ];
    }

    public function testUniqueTableNameOnBindToWorksheet(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $sheet = $this->getSheet();

        $table1 = new Table();
        $table1->setName('Table_1');
        $sheet->addTable($table1);

        $table2 = new Table();
        $table2->setName('tABlE_1'); // case insensitive
        $sheet->addTable($table2);
    }

    public function testUniqueTableNameOnNameChange(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $sheet = $this->getSheet();

        $table1 = new Table();
        $table1->setName('Table_1');
        $sheet->addTable($table1);

        $table2 = new Table();
        $table2->setName('table_2'); // case insensitive
        $sheet->addTable($table2);
        $table2->setName('tAbLe_1');
    }

    public function testVariousSets(): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $result = $table->setShowHeaderRow(false);
        self::assertInstanceOf(Table::class, $result);
        self::assertFalse($table->getShowHeaderRow());

        $result = $table->setShowTotalsRow(true);
        self::assertInstanceOf(Table::class, $result);
        self::assertTrue($table->getShowTotalsRow());
    }

    public function testGetWorksheet(): void
    {
        $sheet = $this->getSheet();
        $table = new Table(self::INITIAL_RANGE);
        $sheet->addTable($table);
        $result = $table->getWorksheet();
        self::assertSame($sheet, $result);
    }

    public function testSetWorksheet(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $spreadsheet = $this->getSpreadsheet();
        $sheet2 = $spreadsheet->createSheet();
        //  Setters return the instance to implement the fluent interface
        $result = $table->setWorksheet($sheet2);
        self::assertInstanceOf(Table::class, $result);
    }

    public function testGetRange(): void
    {
        $expectedResult = self::INITIAL_RANGE;
        $table = new Table(self::INITIAL_RANGE);

        //  Result should be the active table range
        $result = $table->getRange();
        self::assertEquals($expectedResult, $result);
    }

    /**
     * @param AddressRange<CellAddress>|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|string $fullRange
     */
    public function xtestSetRangeValidRange(string|array|AddressRange $fullRange, string $actualRange): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $result = $table->setRange($fullRange);
        self::assertInstanceOf(Table::class, $result);
        self::assertEquals($actualRange, $table->getRange());
    }

    public function testSetRangeValidRange(): void
    {
        foreach ($this->validTableRanges() as $arrayEntry) {
            $this->xtestSetRangeValidRange($arrayEntry[0], $arrayEntry[1]);
        }
    }

    public function validTableRanges(): array
    {
        $sheet = $this->getSheet();
        $title = $sheet->getTitle();

        return [
            ["$title!G1:J512", 'G1:J512'],
            ['K1:N20', 'K1:N20'],
            [[3, 5, 6, 8], 'C5:F8'],
            [new CellRange(new CellAddress('C5', $sheet), new CellAddress('F8', $sheet)), 'C5:F8'],
        ];
    }

    public function testClearRange(): void
    {
        $expectedResult = '';
        $table = new Table(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $result = $table->setRange('');
        self::assertInstanceOf(Table::class, $result);

        //  Result should be a clear range
        $result = $table->getRange();
        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider invalidTableRangeProvider
     */
    public function testSetRangeInvalidRange(string $range): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        new Table($range);
    }

    public static function invalidTableRangeProvider(): array
    {
        return [
            ['A1'],
            ['B1:A4'],
            ['B12:B4'],
            ['D1:A1'],
        ];
    }

    public function testGetColumnsEmpty(): void
    {
        //  There should be no columns yet defined
        $table = new Table(self::INITIAL_RANGE);
        $result = $table->getColumns();
        self::assertIsArray($result);
        self::assertCount(0, $result);
    }

    public function testGetColumnOffset(): void
    {
        $columnIndexes = [
            'H' => 0,
            'K' => 3,
            'M' => 5,
        ];
        $table = new Table(self::INITIAL_RANGE);

        //  If we request a specific column by its column ID, we should get an
        //    integer returned representing the column offset within the range
        foreach ($columnIndexes as $columnIndex => $columnOffset) {
            $result = $table->getColumnOffset($columnIndex);
            self::assertEquals($columnOffset, $result);
        }
    }

    public function testRemoveColumns(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $table = new Table(self::INITIAL_RANGE);
        $table->getColumn('L')->setShowFilterButton(false);
        $sheet->addTable($table);

        $sheet->removeColumn('K', 2);
        $result = $table->getRange();
        self::assertEquals('H2:M256', $result);

        // Check that the prop that was set for column L is no longer set
        self::assertTrue($table->getColumn('L')->getShowFilterButton());
    }

    public function testRemoveRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $table = new Table(self::INITIAL_RANGE);
        $sheet->addTable($table);

        $sheet->removeRow(42, 128);
        $result = $table->getRange();
        self::assertEquals('H2:O128', $result);
    }

    public function testInsertColumns(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $table = new Table(self::INITIAL_RANGE);
        $table->getColumn('N')->setShowFilterButton(false);
        $sheet->addTable($table);

        $sheet->insertNewColumnBefore('N', 3);
        $result = $table->getRange();
        self::assertEquals('H2:R256', $result);

        // Check that column N no longer has a prop
        self::assertTrue($table->getColumn('N')->getShowFilterButton());
        // Check that the prop originally set in column N has been moved to column Q
        self::assertFalse($table->getColumn('Q')->getShowFilterButton());
    }

    public function testInsertRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $table = new Table(self::INITIAL_RANGE);
        $sheet->addTable($table);

        $sheet->insertNewRowBefore(3, 4);
        $result = $table->getRange();
        self::assertEquals('H2:O260', $result);
    }

    public function testGetInvalidColumnOffset(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $invalidColumn = 'G';
        $sheet = $this->getSheet();
        $table = new Table();
        $table->setWorksheet($sheet);

        $table->getColumnOffset($invalidColumn);
    }

    public function testSetColumnWithString(): void
    {
        $expectedResult = 'L';
        $table = new Table(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $result = $table->setColumn($expectedResult);
        self::assertInstanceOf(Table::class, $result);

        $result = $table->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column
        //    objects for each column we set indexed by the column ID
        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
        self::assertInstanceOf(Column::class, $result[$expectedResult]);
    }

    public function testSetInvalidColumnWithString(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $table = new Table(self::INITIAL_RANGE);

        $invalidColumn = 'A';
        $table->setColumn($invalidColumn);
    }

    public function testSetColumnWithColumnObject(): void
    {
        $expectedResult = 'M';
        $columnObject = new Column($expectedResult);
        $table = new Table(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $result = $table->setColumn($columnObject);
        self::assertInstanceOf(Table::class, $result);

        $result = $table->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column
        //    objects for each column we set indexed by the column ID
        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
        self::assertInstanceOf(Column::class, $result[$expectedResult]);
    }

    public function testSetInvalidColumnWithObject(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $invalidColumn = 'E';
        $table = new Table(self::INITIAL_RANGE);
        $table->setColumn($invalidColumn);
    }

    public function testGetColumns(): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $table->setColumn($columnIndex);
        }

        $result = $table->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column
        //    objects for each column we set indexed by the column ID
        self::assertIsArray($result);
        self::assertCount(count($columnIndexes), $result);
        foreach ($columnIndexes as $columnIndex) {
            self::assertArrayHasKey($columnIndex, $result);
            self::assertInstanceOf(Column::class, $result[$columnIndex]);
        }

        $table->setRange('');
        self::assertCount(0, $table->getColumns());
        self::assertSame('', $table->getRange());
    }

    public function testGetColumn(): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $table->setColumn($columnIndex);
        }

        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column object returned
        foreach ($columnIndexes as $columnIndex) {
            $result = $table->getColumn($columnIndex);
            self::assertInstanceOf(Column::class, $result);
        }
    }

    public function testGetColumnByOffset(): void
    {
        $table = new Table(self::INITIAL_RANGE);

        $columnIndexes = [
            0 => 'H',
            3 => 'K',
            5 => 'M',
        ];

        //  If we request a specific column by its offset, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column object returned
        foreach ($columnIndexes as $columnIndex => $columnID) {
            $result = $table->getColumnByOffset($columnIndex);
            self::assertInstanceOf(Column::class, $result);
            self::assertEquals($result->getColumnIndex(), $columnID);
        }
    }

    public function testGetColumnIfNotSet(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table\Column object returned
        $result = $table->getColumn('K');
        self::assertInstanceOf(Column::class, $result);
    }

    public function testGetColumnWithoutRangeSet(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $table = new Table(self::INITIAL_RANGE);

        //  Clear the range
        $table->setRange('');
        $table->getColumn('A');
    }

    public function testClearRangeWithExistingColumns(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $expectedResult = '';

        $columnIndexes = ['L', 'M', 'N'];
        foreach ($columnIndexes as $columnIndex) {
            $table->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $result = $table->setRange('');
        self::assertInstanceOf(Table::class, $result);

        //  Range should be cleared
        $result = $table->getRange();
        self::assertEquals($expectedResult, $result);

        //  Column array should be cleared
        $result = $table->getColumns();
        self::assertIsArray($result);
        self::assertCount(0, $result);
    }

    public function testSetRangeWithExistingColumns(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $expectedResult = 'G1:J512';

        //  These columns should be retained
        $columnIndexes1 = ['I', 'J'];
        foreach ($columnIndexes1 as $columnIndex) {
            $table->setColumn($columnIndex);
        }
        //  These columns should be discarded
        $columnIndexes2 = ['K', 'L', 'M'];
        foreach ($columnIndexes2 as $columnIndex) {
            $table->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $result = $table->setRange($expectedResult);
        self::assertInstanceOf(Table::class, $result);

        //  Range should be correctly set
        $result = $table->getRange();
        self::assertEquals($expectedResult, $result);

        //  Only columns that existed in the original range and that
        //    still fall within the new range should be retained
        $result = $table->getColumns();
        self::assertIsArray($result);
        self::assertCount(count($columnIndexes1), $result);
    }

    public function testClone(): void
    {
        $sheet = $this->getSheet();
        $table = new Table(self::INITIAL_RANGE);
        $sheet->addTable($table);
        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $table->setColumn($columnIndex);
        }

        $result = clone $table;
        self::assertInstanceOf(Table::class, $result);
        self::assertSame($table->getRange(), $result->getRange());
        self::assertNull($result->getWorksheet());
        self::assertNotNull($table->getWorksheet());
        self::assertInstanceOf(Worksheet::class, $table->getWorksheet());
        $tableColumns = $table->getColumns();
        $resultColumns = $result->getColumns();
        self::assertIsArray($tableColumns);
        self::assertIsArray($resultColumns);
        self::assertCount(2, $tableColumns);
        self::assertCount(2, $resultColumns);
        self::assertArrayHasKey('L', $tableColumns);
        self::assertArrayHasKey('L', $resultColumns);
        self::assertArrayHasKey('M', $tableColumns);
        self::assertArrayHasKey('M', $resultColumns);
        self::assertInstanceOf(Column::class, $tableColumns['L']);
        self::assertInstanceOf(Column::class, $resultColumns['L']);
        self::assertInstanceOf(Column::class, $tableColumns['M']);
        self::assertInstanceOf(Column::class, $resultColumns['M']);
    }

    public function testNoWorksheet(): void
    {
        $table = new Table();
        self::assertNull($table->getWorksheet());
    }

    public function testClearColumn(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $columnIndexes = ['J', 'K', 'L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $table->setColumn($columnIndex);
        }
        $columns = $table->getColumns();
        self::assertCount(4, $columns);
        self::assertArrayHasKey('J', $columns);
        self::assertArrayHasKey('K', $columns);
        self::assertArrayHasKey('L', $columns);
        self::assertArrayHasKey('M', $columns);
        $table->clearColumn('K');
        $columns = $table->getColumns();
        self::assertCount(3, $columns);
        self::assertArrayHasKey('J', $columns);
        self::assertArrayHasKey('L', $columns);
        self::assertArrayHasKey('M', $columns);
    }

    public function testAutoFilterRule(): void
    {
        $table = new Table(self::INITIAL_RANGE);
        $columnFilter = $table->getAutoFilter()->getColumn('H');
        $columnFilter->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $autoFilterRuleObject = new AutoFilter\Column\Rule($columnFilter);
        self::assertEquals(AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_FILTER, $autoFilterRuleObject->getRuleType());
        $ruleParent = $autoFilterRuleObject->getParent();
        if ($ruleParent === null) {
            self::fail('Unexpected null parent');
        } else {
            self::assertEquals('H', $ruleParent->getColumnIndex());
            self::assertSame($columnFilter, $ruleParent);
        }
    }
}
