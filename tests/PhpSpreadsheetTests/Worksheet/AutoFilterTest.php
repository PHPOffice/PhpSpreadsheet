<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class AutoFilterTest extends TestCase
{
    private $testInitialRange = 'H2:O256';

    /**
     * @var AutoFilter
     */
    private $testAutoFilterObject;

    private $mockWorksheetObject;

    private $cellCollection;

    public function setUp()
    {
        $this->mockWorksheetObject = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cellCollection = $this->getMockBuilder(Cells::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheetObject->expects($this->any())
            ->method('getCellCollection')
            ->will($this->returnValue($this->cellCollection));

        $this->testAutoFilterObject = new AutoFilter($this->testInitialRange, $this->mockWorksheetObject);
    }

    public function testToString()
    {
        $expectedResult = $this->testInitialRange;

        //  magic __toString should return the active autofilter range
        $result = $this->testAutoFilterObject;
        self::assertEquals($expectedResult, $result);
    }

    public function testGetParent()
    {
        $result = $this->testAutoFilterObject->getParent();
        self::assertInstanceOf(Worksheet::class, $result);
    }

    public function testSetParent()
    {
        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setParent($this->mockWorksheetObject);
        self::assertInstanceOf(AutoFilter::class, $result);
    }

    public function testGetRange()
    {
        $expectedResult = $this->testInitialRange;

        //  Result should be the active autofilter range
        $result = $this->testAutoFilterObject->getRange();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetRange()
    {
        $ranges = [
            'G1:J512' => 'Worksheet1!G1:J512',
            'K1:N20' => 'K1:N20',
        ];

        foreach ($ranges as $actualRange => $fullRange) {
            //  Setters return the instance to implement the fluent interface
            $result = $this->testAutoFilterObject->setRange($fullRange);
            self::assertInstanceOf(AutoFilter::class, $result);

            //  Result should be the new autofilter range
            $result = $this->testAutoFilterObject->getRange();
            self::assertEquals($actualRange, $result);
        }
    }

    public function testClearRange()
    {
        $expectedResult = '';

        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setRange('');
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Result should be a clear range
        $result = $this->testAutoFilterObject->getRange();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetRangeInvalidRange()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $expectedResult = 'A1';

        $this->testAutoFilterObject->setRange($expectedResult);
    }

    public function testGetColumnsEmpty()
    {
        //  There should be no columns yet defined
        $result = $this->testAutoFilterObject->getColumns();
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    public function testGetColumnOffset()
    {
        $columnIndexes = [
            'H' => 0,
            'K' => 3,
            'M' => 5,
        ];

        //  If we request a specific column by its column ID, we should get an
        //    integer returned representing the column offset within the range
        foreach ($columnIndexes as $columnIndex => $columnOffset) {
            $result = $this->testAutoFilterObject->getColumnOffset($columnIndex);
            self::assertEquals($columnOffset, $result);
        }
    }

    public function testGetInvalidColumnOffset()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $invalidColumn = 'G';

        $this->testAutoFilterObject->getColumnOffset($invalidColumn);
    }

    public function testSetColumnWithString()
    {
        $expectedResult = 'L';

        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setColumn($expectedResult);
        self::assertInstanceOf(AutoFilter::class, $result);

        $result = $this->testAutoFilterObject->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertInternalType('array', $result);
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
        self::assertInstanceOf(Column::class, $result[$expectedResult]);
    }

    public function testSetInvalidColumnWithString()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $invalidColumn = 'A';

        $this->testAutoFilterObject->setColumn($invalidColumn);
    }

    public function testSetColumnWithColumnObject()
    {
        $expectedResult = 'M';
        $columnObject = new AutoFilter\Column($expectedResult);

        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setColumn($columnObject);
        self::assertInstanceOf(AutoFilter::class, $result);

        $result = $this->testAutoFilterObject->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertInternalType('array', $result);
        self::assertCount(1, $result);
        self::assertArrayHasKey($expectedResult, $result);
        self::assertInstanceOf(Column::class, $result[$expectedResult]);
    }

    public function testSetInvalidColumnWithObject()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $invalidColumn = 'E';
        $this->testAutoFilterObject->setColumn($invalidColumn);
    }

    public function testSetColumnWithInvalidDataType()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $invalidColumn = 123.456;
        $this->testAutoFilterObject->setColumn($invalidColumn);
    }

    public function testGetColumns()
    {
        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }

        $result = $this->testAutoFilterObject->getColumns();
        //  Result should be an array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column
        //    objects for each column we set indexed by the column ID
        self::assertInternalType('array', $result);
        self::assertCount(count($columnIndexes), $result);
        foreach ($columnIndexes as $columnIndex) {
            self::assertArrayHasKey($columnIndex, $result);
            self::assertInstanceOf(Column::class, $result[$columnIndex]);
        }
    }

    public function testGetColumn()
    {
        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }

        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        foreach ($columnIndexes as $columnIndex) {
            $result = $this->testAutoFilterObject->getColumn($columnIndex);
            self::assertInstanceOf(Column::class, $result);
        }
    }

    public function testGetColumnByOffset()
    {
        $columnIndexes = [
            0 => 'H',
            3 => 'K',
            5 => 'M',
        ];

        //  If we request a specific column by its offset, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        foreach ($columnIndexes as $columnIndex => $columnID) {
            $result = $this->testAutoFilterObject->getColumnByOffset($columnIndex);
            self::assertInstanceOf(Column::class, $result);
            self::assertEquals($result->getColumnIndex(), $columnID);
        }
    }

    public function testGetColumnIfNotSet()
    {
        //  If we request a specific column by its column ID, we should
        //    get a \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\AutoFilter\Column object returned
        $result = $this->testAutoFilterObject->getColumn('K');
        self::assertInstanceOf(Column::class, $result);
    }

    public function testGetColumnWithoutRangeSet()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        //  Clear the range
        $this->testAutoFilterObject->setRange('');
        $this->testAutoFilterObject->getColumn('A');
    }

    public function testClearRangeWithExistingColumns()
    {
        $expectedResult = '';

        $columnIndexes = ['L', 'M', 'N'];
        foreach ($columnIndexes as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setRange('');
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Range should be cleared
        $result = $this->testAutoFilterObject->getRange();
        self::assertEquals($expectedResult, $result);

        //  Column array should be cleared
        $result = $this->testAutoFilterObject->getColumns();
        self::assertInternalType('array', $result);
        self::assertCount(0, $result);
    }

    public function testSetRangeWithExistingColumns()
    {
        $expectedResult = 'G1:J512';

        //  These columns should be retained
        $columnIndexes1 = ['I', 'J'];
        foreach ($columnIndexes1 as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }
        //  These columns should be discarded
        $columnIndexes2 = ['K', 'L', 'M'];
        foreach ($columnIndexes2 as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }

        //  Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterObject->setRange($expectedResult);
        self::assertInstanceOf(AutoFilter::class, $result);

        //  Range should be correctly set
        $result = $this->testAutoFilterObject->getRange();
        self::assertEquals($expectedResult, $result);

        //  Only columns that existed in the original range and that
        //    still fall within the new range should be retained
        $result = $this->testAutoFilterObject->getColumns();
        self::assertInternalType('array', $result);
        self::assertCount(count($columnIndexes1), $result);
    }

    public function testClone()
    {
        $columnIndexes = ['L', 'M'];

        foreach ($columnIndexes as $columnIndex) {
            $this->testAutoFilterObject->setColumn($columnIndex);
        }

        $result = clone $this->testAutoFilterObject;
        self::assertInstanceOf(AutoFilter::class, $result);
    }
}
