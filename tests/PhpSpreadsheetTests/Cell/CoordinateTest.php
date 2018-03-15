<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PHPUnit\Framework\TestCase;

class CoordinateTest extends TestCase
{
    /**
     * @dataProvider providerColumnString
     *
     * @param mixed $expectedResult
     * @param mixed $string
     */
    public function testColumnIndexFromString($expectedResult, $string)
    {
        $columnIndex = Coordinate::columnIndexFromString($string);
        self::assertEquals($expectedResult, $columnIndex);

        $stringBack = Coordinate::stringFromColumnIndex($columnIndex);
        self::assertEquals($stringBack, $string, 'should be able to get the original input with opposite method');
    }

    public function providerColumnString()
    {
        return require 'data/ColumnString.php';
    }

    public function testColumnIndexFromStringTooLong()
    {
        $cellAddress = 'ABCD';

        try {
            Coordinate::columnIndexFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Column string index can not be longer than 3 characters');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testColumnIndexFromStringTooShort()
    {
        $cellAddress = '';

        try {
            Coordinate::columnIndexFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Column string index can not be empty');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerColumnIndex
     *
     * @param mixed $expectedResult
     * @param int $columnIndex
     */
    public function testStringFromColumnIndex($expectedResult, $columnIndex)
    {
        $string = Coordinate::stringFromColumnIndex($columnIndex);
        self::assertEquals($expectedResult, $string);

        $columnIndexBack = Coordinate::columnIndexFromString($string);
        self::assertEquals($columnIndexBack, $columnIndex, 'should be able to get the original input with opposite method');
    }

    public function providerColumnIndex()
    {
        return require 'data/ColumnIndex.php';
    }

    /**
     * @dataProvider providerCoordinates
     *
     * @param mixed $expectedResult
     */
    public function testCoordinateFromString($expectedResult, ...$args)
    {
        $result = Coordinate::coordinateFromString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCoordinates()
    {
        return require 'data/CellCoordinates.php';
    }

    public function testCoordinateFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithEmptyAddress()
    {
        $cellAddress = '';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate can not be zero-length string');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithInvalidAddress()
    {
        $cellAddress = 'AI';

        try {
            Coordinate::coordinateFromString($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Invalid cell coordinate ' . $cellAddress);

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteCoordinates
     *
     * @param mixed $expectedResult
     */
    public function testAbsoluteCoordinateFromString($expectedResult, ...$args)
    {
        $result = Coordinate::absoluteCoordinate(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteCoordinates()
    {
        return require 'data/CellAbsoluteCoordinate.php';
    }

    public function testAbsoluteCoordinateFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::absoluteCoordinate($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteReferences
     *
     * @param mixed $expectedResult
     */
    public function testAbsoluteReferenceFromString($expectedResult, ...$args)
    {
        $result = Coordinate::absoluteReference(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteReferences()
    {
        return require 'data/CellAbsoluteReference.php';
    }

    public function testAbsoluteReferenceFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';

        try {
            Coordinate::absoluteReference($cellAddress);
        } catch (\Exception $e) {
            self::assertInstanceOf(Exception::class, $e);
            self::assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerSplitRange
     *
     * @param mixed $expectedResult
     */
    public function testSplitRange($expectedResult, ...$args)
    {
        $result = Coordinate::splitRange(...$args);
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
        return require 'data/CellSplitRange.php';
    }

    /**
     * @dataProvider providerBuildRange
     *
     * @param mixed $expectedResult
     */
    public function testBuildRange($expectedResult, ...$args)
    {
        $result = Coordinate::buildRange(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBuildRange()
    {
        return require 'data/CellBuildRange.php';
    }

    public function testBuildRangeInvalid()
    {
        $this->expectException(\TypeError::class);

        if (PHP_MAJOR_VERSION < 7) {
            $this->markTestSkipped('Cannot catch type hinting error with PHP 5.6');
        }

        $cellRange = '';
        Coordinate::buildRange($cellRange);
    }

    /**
     * @dataProvider providerRangeBoundaries
     *
     * @param mixed $expectedResult
     */
    public function testRangeBoundaries($expectedResult, ...$args)
    {
        $result = Coordinate::rangeBoundaries(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRangeBoundaries()
    {
        return require 'data/CellRangeBoundaries.php';
    }

    /**
     * @dataProvider providerRangeDimension
     *
     * @param mixed $expectedResult
     */
    public function testRangeDimension($expectedResult, ...$args)
    {
        $result = Coordinate::rangeDimension(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRangeDimension()
    {
        return require 'data/CellRangeDimension.php';
    }

    /**
     * @dataProvider providerGetRangeBoundaries
     *
     * @param mixed $expectedResult
     */
    public function testGetRangeBoundaries($expectedResult, ...$args)
    {
        $result = Coordinate::getRangeBoundaries(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerGetRangeBoundaries()
    {
        return require 'data/CellGetRangeBoundaries.php';
    }

    /**
     * @dataProvider providerExtractAllCellReferencesInRange
     *
     * @param mixed $expectedResult
     */
    public function testExtractAllCellReferencesInRange($expectedResult, ...$args)
    {
        $result = Coordinate::extractAllCellReferencesInRange(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerExtractAllCellReferencesInRange()
    {
        return require 'data/CellExtractAllCellReferencesInRange.php';
    }

    /**
     * @dataProvider providerMergeRangesInCollection
     *
     * @param mixed $expectedResult
     */
    public function testMergeRangesInCollection($expectedResult, ...$args)
    {
        $result = Coordinate::mergeRangesInCollection(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMergeRangesInCollection()
    {
        return require 'data/CellMergeRangesInCollection.php';
    }

    /**
     * @dataProvider providerCoordinateIsRange
     *
     * @param mixed $expectedResult
     */
    public function testCoordinateIsRange($expectedResult, ...$args)
    {
        $result = Coordinate::coordinateIsRange(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCoordinateIsRange()
    {
        return require 'data/CoordinateIsRange.php';
    }
}
