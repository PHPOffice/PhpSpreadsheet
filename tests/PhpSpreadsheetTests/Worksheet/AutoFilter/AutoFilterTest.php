<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilterTest extends SetupTeardown
{
    private const INITIAL_RANGE = 'H2:O256';

    public function testToString(): void
    {
        $expectedResult = self::INITIAL_RANGE;
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  magic __toString should return the active autofilter range
        $result = (string) $autoFilter;
        self::assertEquals($expectedResult, $result);
    }

    public function testGetParent(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $result = $autoFilter->getParent();
        self::assertSame($sheet, $result);
    }

    public function testSetParent(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $spreadsheet = $this->getSpreadsheet();
        $sheet2 = $spreadsheet->createSheet();
        //  Setters return the instance to implement the fluent interface
        $result = $autoFilter->setParent($sheet2);
        self::assertSame(self::INITIAL_RANGE, $result->getRange());
    }

    public function testGetRange(): void
    {
        $expectedResult = self::INITIAL_RANGE;
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  Result should be the active autofilter range
        $result = $autoFilter->getRange();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetRange(): void
    {
        $sheet = $this->getSheet();
        $title = $sheet->getTitle();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $ranges = [
            'G1:J512' => "$title!G1:J512",
            'K1:N20' => 'K1:N20',
            'B10' => 'B10',
        ];

        foreach ($ranges as $actualRange => $fullRange) {
            //  Setters return the instance to implement the fluent interface
            $temp = $autoFilter->setRange($fullRange);

            //  Result should be the new autofilter range
            $result = $temp->getRange();
            self::assertEquals($actualRange, $result);
        }
    }

    public function testClearRange(): void
    {
        $expectedResult = '';
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $temp = $autoFilter->setRange('');

        //  Result should be a clear range
        $result = $temp->getRange();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetRangeInvalidRowRange(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $expectedResult = '999';

        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange($expectedResult);
    }

    public function testSetRangeInvalidColumnRange(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $expectedResult = 'ABC';

        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange($expectedResult);
    }

    public function testGetColumnsEmpty(): void
    {
        //  There should be no columns yet defined
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $result = $autoFilter->getColumns();
        self::assertSame([], $result);
    }

    public function testGetColumnOffset(): void
    {
        $columnIndexes = [
            'H' => 0,
            'K' => 3,
            'M' => 5,
        ];
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  If we request a specific column by its column ID, we should get an
        //    integer returned representing the column offset within the range
        foreach ($columnIndexes as $columnIndex => $columnOffset) {
            $result = $autoFilter->getColumnOffset($columnIndex);
            self::assertEquals($columnOffset, $result);
        }
    }

    public function testRemoveColumns(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $autoFilter->getColumn('L')->addRule((new Column\Rule())->setValue(5));

        $sheet->removeColumn('K', 2);
        $result = $autoFilter->getRange();
        self::assertEquals('H2:M256', $result);

        // Check that the rule that was set for column L is no longer set
        self::assertEmpty($autoFilter->getColumn('L')->getRule(0)->getValue());
    }

    public function testRemoveRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $sheet->removeRow(42, 128);
        $result = $autoFilter->getRange();
        self::assertEquals('H2:O128', $result);
    }

    public function testInsertColumns(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $autoFilter->getColumn('N')->addRule((new Column\Rule())->setValue(5));

        $sheet->insertNewColumnBefore('N', 3);
        $result = $autoFilter->getRange();
        self::assertEquals('H2:R256', $result);

        // Check that column N no longer has a rule set
        self::assertEmpty($autoFilter->getColumn('N')->getRule(0)->getValue());
        // Check that the rule originally set in column N has been moved to column Q
        self::assertSame(5, $autoFilter->getColumn('Q')->getRule(0)->getValue());
    }

    public function testInsertRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(range('H', 'O'), null, 'H2');
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $sheet->insertNewRowBefore(3, 4);
        $result = $autoFilter->getRange();
        self::assertEquals('H2:O260', $result);
    }

    public function testGetInvalidColumnOffset(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $invalidColumn = 'G';
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $autoFilter->getColumnOffset($invalidColumn);
    }

    public function testSetColumnWithString(): void
    {
        $expectedResult = 'L';
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $temp = $autoFilter->setColumn($expectedResult);

        $result = $temp->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
    }

    public function testSetInvalidColumnWithString(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $invalidColumn = 'A';

        $autoFilter->setColumn($invalidColumn);
    }

    public function testSetColumnWithColumnObject(): void
    {
        $expectedResult = 'M';
        $columnObject = new Column($expectedResult);
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  Setters return the instance to implement the fluent interface
        $temp = $autoFilter->setColumn($columnObject);

        $result = $temp->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
    }

    public function testSetInvalidColumnWithObject(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $invalidColumn = 'E';
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $autoFilter->setColumn($invalidColumn);
    }

    public function testGetColumns(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }

        $result = $autoFilter->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertCount(count($columnIndexes), $result);
        foreach ($columnIndexes as $columnIndex) {
            self::assertArrayHasKey($columnIndex, $result);
        }

        $autoFilter->setRange('');
        self::assertCount(0, $autoFilter->getColumns());
        self::assertSame('', $autoFilter->getRange());
    }

    public function testGetColumn(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }

        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        foreach ($columnIndexes as $columnIndex) {
            $result = $autoFilter->getColumn($columnIndex);
            self::assertSame($columnIndex, $result->getColumnIndex());
        }
    }

    public function testGetColumnByOffset(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        $columnIndexes = [
            0 => 'H',
            3 => 'K',
            5 => 'M',
        ];

        //  If we request a specific column by its offset, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        foreach ($columnIndexes as $columnIndex => $columnID) {
            $result = $autoFilter->getColumnByOffset($columnIndex);
            self::assertEquals($result->getColumnIndex(), $columnID);
        }
    }

    public function testGetColumnIfNotSet(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        $result = $autoFilter->getColumn('K');
        self::assertSame('K', $result->getColumnIndex());
    }

    public function testGetColumnWithoutRangeSet(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);

        //  Clear the range
        $autoFilter->setRange('');
        $autoFilter->getColumn('A');
    }

    public function testClearRangeWithExistingColumns(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $expectedResult = '';

        $columnIndexes = ['L', 'M', 'N'];
        foreach ($columnIndexes as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $temp = $autoFilter->setRange('');

        //  Range should be cleared
        $result = $temp->getRange();
        self::assertEquals($expectedResult, $result);

        //  Column array should be cleared
        $result = $autoFilter->getColumns();
        self::assertSame([], $result);
    }

    public function testSetRangeWithExistingColumns(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $expectedResult = 'G1:J512';

        //  These columns should be retained
        $columnIndexes1 = ['I', 'J'];
        foreach ($columnIndexes1 as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }
        //  These columns should be discarded
        $columnIndexes2 = ['K', 'L', 'M'];
        foreach ($columnIndexes2 as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $temp = $autoFilter->setRange($expectedResult);

        //  Range should be correctly set
        $result = $temp->getRange();
        self::assertEquals($expectedResult, $result);

        //  Only columns that existed in the original range and that
        //    still fall within the new range should be retained
        $result = $autoFilter->getColumns();
        self::assertCount(count($columnIndexes1), $result);
    }

    public function testClone(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }

        $result = clone $autoFilter;
        self::assertSame($autoFilter->getRange(), $result->getRange());
        self::assertNull($result->getParent());
        self::assertInstanceOf(Worksheet::class, $autoFilter->getParent());
        $autoColumns = $autoFilter->getColumns();
        $resultColumns = $result->getColumns();
        self::assertCount(2, $autoColumns);
        self::assertCount(2, $resultColumns);
        self::assertArrayHasKey('L', $autoColumns);
        self::assertArrayHasKey('L', $resultColumns);
        self::assertArrayHasKey('M', $autoColumns);
        self::assertArrayHasKey('M', $resultColumns);
    }

    public function testNoWorksheet(): void
    {
        $autoFilter = new AutoFilter();
        self::assertSame($autoFilter, $autoFilter->showHideRows());
    }

    public function testClearColumn(): void
    {
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $columnIndexes = ['J', 'K', 'L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $autoFilter->setColumn($columnIndex);
        }
        $columns = $autoFilter->getColumns();
        self::assertCount(4, $columns);
        self::assertArrayHasKey('J', $columns);
        self::assertArrayHasKey('K', $columns);
        self::assertArrayHasKey('L', $columns);
        self::assertArrayHasKey('M', $columns);
        $autoFilter->clearColumn('K');
        $columns = $autoFilter->getColumns();
        self::assertCount(3, $columns);
        self::assertArrayHasKey('J', $columns);
        self::assertArrayHasKey('L', $columns);
        self::assertArrayHasKey('M', $columns);
        $autoFilter->shiftColumn('L', 'K');
        $columns = $autoFilter->getColumns();
        self::assertCount(3, $columns);
        self::assertArrayHasKey('J', $columns);
        self::assertArrayHasKey('K', $columns);
        self::assertArrayHasKey('M', $columns);
    }

    public function testAutoExtendRange(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $worksheet = $spreadsheet->addSheet(new Worksheet($spreadsheet, 'Autosized AutoFilter'));

        $worksheet->getCell('A1')->setValue('Col 1');
        $worksheet->getCell('B1')->setValue('Col 2');

        $worksheet->setAutoFilter('A1:B1');
        $lastRow = $worksheet->getAutoFilter()->autoExtendRange(1, 1);
        self::assertSame(1, $lastRow, 'No data below AutoFilter, so there should ne no resize');

        $lastRow = $worksheet->getAutoFilter()->autoExtendRange(1, 999);
        self::assertSame(999, $lastRow, 'Filter range is already correctly sized');

        $data = [['A', 'A'], ['B', 'A'], ['A', 'B'], ['C', 'B'], ['B', null], [null, null], ['D', 'D'], ['E', 'E']];
        $worksheet->fromArray($data, null, 'A2', true);

        $lastRow = $worksheet->getAutoFilter()->autoExtendRange(1, 1);
        self::assertSame(6, $lastRow, 'Filter range has been re-sized incorrectly');
    }
}
