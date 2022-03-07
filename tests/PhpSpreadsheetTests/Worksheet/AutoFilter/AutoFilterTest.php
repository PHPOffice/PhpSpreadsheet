<?php

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
        self::assertInstanceOf(AutoFilter::class, $result);
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
        ];

        foreach ($ranges as $actualRange => $fullRange) {
            //  Setters return the instance to implement the fluent interface
            $result = $autoFilter->setRange($fullRange);
            self::assertInstanceOf(AutoFilter::class, $result);

            //  Result should be the new autofilter range
            $result = $autoFilter->getRange();
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
        $result = $autoFilter->setRange('');
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Result should be a clear range
        $result = $autoFilter->getRange();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetRangeInvalidRange(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $expectedResult = 'A1';

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
        $result = $autoFilter->setColumn($expectedResult);
        self::assertInstanceOf(AutoFilter::class, $result);

        $result = $autoFilter->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
        self::assertInstanceOf(Column::class, $result[$expectedResult]);
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
        $result = $autoFilter->setColumn($columnObject);
        self::assertInstanceOf(AutoFilter::class, $result);

        $result = $autoFilter->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
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
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $autoFilter->setColumn($invalidColumn);
    }

    public function testSetColumnWithInvalidDataType(): void
    {
        $this->expectException(PhpSpreadsheetException::class);

        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange(self::INITIAL_RANGE);
        $invalidColumn = 123.456;
        // @phpstan-ignore-next-line
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
        self::assertIsArray($result);
        self::assertCount(count($columnIndexes), $result);
        foreach ($columnIndexes as $columnIndex) {
            self::assertArrayHasKey($columnIndex, $result);
            self::assertInstanceOf(Column::class, $result[$columnIndex]);
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
            self::assertInstanceOf(Column::class, $result);
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
            self::assertInstanceOf(Column::class, $result);
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
        self::assertInstanceOf(Column::class, $result);
    }

    public function testGetColumnWithoutRangeSet(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
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
        $result = $autoFilter->setRange('');
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Range should be cleared
        $result = $autoFilter->getRange();
        self::assertEquals($expectedResult, $result);

        //  Column array should be cleared
        $result = $autoFilter->getColumns();
        self::assertIsArray($result);
        self::assertCount(0, $result);
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
        $result = $autoFilter->setRange($expectedResult);
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Range should be correctly set
        $result = $autoFilter->getRange();
        self::assertEquals($expectedResult, $result);

        //  Only columns that existed in the original range and that
        //    still fall within the new range should be retained
        $result = $autoFilter->getColumns();
        self::assertIsArray($result);
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
        self::assertInstanceOf(AutoFilter::class, $result);
        self::assertSame($autoFilter->getRange(), $result->getRange());
        self::assertNull($result->getParent());
        self::assertNotNull($autoFilter->getParent());
        self::assertInstanceOf(Worksheet::class, $autoFilter->getParent());
        $autoColumns = $autoFilter->getColumns();
        $resultColumns = $result->getColumns();
        self::assertIsArray($autoColumns);
        self::assertIsArray($resultColumns);
        self::assertCount(2, $autoColumns);
        self::assertCount(2, $resultColumns);
        self::assertArrayHasKey('L', $autoColumns);
        self::assertArrayHasKey('L', $resultColumns);
        self::assertArrayHasKey('M', $autoColumns);
        self::assertArrayHasKey('M', $resultColumns);
        self::assertInstanceOf(Column::class, $autoColumns['L']);
        self::assertInstanceOf(Column::class, $resultColumns['L']);
        self::assertInstanceOf(Column::class, $autoColumns['M']);
        self::assertInstanceOf(Column::class, $resultColumns['M']);
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
}
