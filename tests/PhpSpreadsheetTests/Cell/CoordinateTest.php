<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

class CoordinateTest extends TestCase
{
    /**
     * @dataProvider providerColumnString
     *
     * @param mixed $expectedResult
     * @param mixed $string
     */
    public function testColumnIndexFromString($expectedResult, $string): void
    {
        $columnIndex = Coordinate::columnIndexFromString($string);
        self::assertEquals($expectedResult, $columnIndex);

        $stringBack = Coordinate::stringFromColumnIndex($columnIndex);
        self::assertEquals($stringBack, $string, 'should be able to get the original input with opposite method');
    }

    public function providerColumnString()
    {
        return require 'tests/data/ColumnString.php';
    }

    public function testColumnIndexFromStringTooLong(): void
    {
        $cellAddress = 'ABCD';

        try {
            Coordinate::columnIndexFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Column string index can not be longer than 3 characters');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    public function testColumnIndexFromStringTooShort(): void
    {
        $cellAddress = '';

        try {
            Coordinate::columnIndexFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Column string index can not be empty');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerColumnIndex
     *
     * @param mixed $expectedResult
     * @param int $columnIndex
     */
    public function testStringFromColumnIndex($expectedResult, $columnIndex): void
    {
        $string = Coordinate::stringFromColumnIndex($columnIndex);
        self::assertEquals($expectedResult, $string);

        $columnIndexBack = Coordinate::columnIndexFromString($string);
        self::assertEquals($columnIndexBack, $columnIndex, 'should be able to get the original input with opposite method');
    }

    public function providerColumnIndex()
    {
        return require 'tests/data/ColumnIndex.php';
    }

    /**
     * @dataProvider providerCoordinates
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testCoordinateFromString($expectedResult, $rangeSet): void
    {
        $result = Coordinate::coordinateFromString($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCoordinates()
    {
        return require 'tests/data/CellCoordinates.php';
    }

    public function testCoordinateFromStringWithRangeAddress(): void
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithEmptyAddress(): void
    {
        $cellAddress = '';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate can not be zero-length string');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithInvalidAddress(): void
    {
        $cellAddress = 'AI';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Invalid cell coordinate ' . $cellAddress);

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteCoordinates
     *
     * @param string $expectedResult
     * @param string $rangeSet
     */
    public function testAbsoluteCoordinateFromString($expectedResult, $rangeSet): void
    {
        $result = Coordinate::absoluteCoordinate($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteCoordinates()
    {
        return require 'tests/data/CellAbsoluteCoordinate.php';
    }

    public function testAbsoluteCoordinateFromStringWithRangeAddress(): void
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::absoluteCoordinate($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteReferences
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testAbsoluteReferenceFromString($expectedResult, $rangeSet): void
    {
        $result = Coordinate::absoluteReference($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteReferences()
    {
        return require 'tests/data/CellAbsoluteReference.php';
    }

    public function testAbsoluteReferenceFromStringWithRangeAddress(): void
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::absoluteReference($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        self::fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerSplitRange
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testSplitRange($expectedResult, $rangeSet): void
    {
        $result = Coordinate::splitRange($rangeSet);
        foreach ($result as $key => $split) {
            if (!is_array($expectedResult[$key])) {
                self::assertEquals($expectedResult[$key], $split[0]);
            } else {
                self::assertEquals($expectedResult[$key], $split);
            }
        }
    }

    public function providerSplitRange()
    {
        return require 'tests/data/CellSplitRange.php';
    }

    /**
     * @dataProvider providerBuildRange
     *
     * @param mixed $expectedResult
     */
    public function testBuildRange($expectedResult, ...$args): void
    {
        $result = Coordinate::buildRange(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBuildRange()
    {
        return require 'tests/data/CellBuildRange.php';
    }

    public function testBuildRangeInvalid(): void
    {
        $this->expectException(TypeError::class);

        $cellRange = '';
        Coordinate::buildRange($cellRange);
    }

    /**
     * @dataProvider providerRangeBoundaries
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testRangeBoundaries($expectedResult, $rangeSet): void
    {
        $result = Coordinate::rangeBoundaries($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRangeBoundaries()
    {
        return require 'tests/data/CellRangeBoundaries.php';
    }

    /**
     * @dataProvider providerRangeDimension
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testRangeDimension($expectedResult, $rangeSet): void
    {
        $result = Coordinate::rangeDimension($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRangeDimension()
    {
        return require 'tests/data/CellRangeDimension.php';
    }

    /**
     * @dataProvider providerGetRangeBoundaries
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testGetRangeBoundaries($expectedResult, $rangeSet): void
    {
        $result = Coordinate::getRangeBoundaries($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerGetRangeBoundaries()
    {
        return require 'tests/data/CellGetRangeBoundaries.php';
    }

    /**
     * @dataProvider providerExtractAllCellReferencesInRange
     *
     * @param array $expectedResult
     * @param string $rangeSet
     */
    public function testExtractAllCellReferencesInRange($expectedResult, $rangeSet): void
    {
        $result = Coordinate::extractAllCellReferencesInRange($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerExtractAllCellReferencesInRange()
    {
        return require 'tests/data/CellExtractAllCellReferencesInRange.php';
    }

    /**
     * @dataProvider providerInvalidRange
     *
     * @param string $range
     */
    public function testExtractAllCellReferencesInRangeInvalidRange($range): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid range: "' . $range . '"');

        Coordinate::extractAllCellReferencesInRange($range);
    }

    public function providerInvalidRange()
    {
        return [['Z1:A1'], ['A4:A1'], ['B1:A1'], ['AA1:Z1']];
    }

    /**
     * @dataProvider providerMergeRangesInCollection
     *
     * @param mixed $expectedResult
     */
    public function testMergeRangesInCollection($expectedResult, ...$args): void
    {
        $result = Coordinate::mergeRangesInCollection(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMergeRangesInCollection()
    {
        return require 'tests/data/CellMergeRangesInCollection.php';
    }

    /**
     * @dataProvider providerCoordinateIsRange
     *
     * @param mixed $expectedResult
     * @param string $rangeSet
     */
    public function testCoordinateIsRange($expectedResult, $rangeSet): void
    {
        $result = Coordinate::coordinateIsRange($rangeSet);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCoordinateIsRange()
    {
        return require 'tests/data/CoordinateIsRange.php';
    }
}
