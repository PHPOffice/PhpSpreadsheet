<?php

namespace PhpSpreadsheet\Tests;

use PHPExcel\Cell;
use PHPExcel\Exception;

class CellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerColumnString
     */
    public function testColumnIndexFromString()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'columnIndexFromString'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColumnString()
    {
        return require 'data/ColumnString.php';
    }

    public function testColumnIndexFromStringTooLong()
    {
        $cellAddress = 'ABCD';
        try {
            $result = call_user_func(array(Cell::class,'columnIndexFromString'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Column string index can not be longer than 3 characters');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testColumnIndexFromStringTooShort()
    {
        $cellAddress = '';
        try {
            $result = call_user_func(array(Cell::class,'columnIndexFromString'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Column string index can not be empty');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerColumnIndex
     */
    public function testStringFromColumnIndex()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'stringFromColumnIndex'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerColumnIndex()
    {
        return require 'data/ColumnIndex.php';
    }

    /**
     * @dataProvider providerCoordinates
     */
    public function testCoordinateFromString()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'coordinateFromString'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCoordinates()
    {
        return require 'data/CellCoordinates.php';
    }

    public function testCoordinateFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(array(Cell::class,'coordinateFromString'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithEmptyAddress()
    {
        $cellAddress = '';
        try {
            $result = call_user_func(array(Cell::class,'coordinateFromString'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Cell coordinate can not be zero-length string');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCoordinateFromStringWithInvalidAddress()
    {
        $cellAddress = 'AI';
        try {
            $result = call_user_func(array(Cell::class,'coordinateFromString'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Invalid cell coordinate '.$cellAddress);
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteCoordinates
     */
    public function testAbsoluteCoordinateFromString()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'absoluteCoordinate'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteCoordinates()
    {
        return require 'data/CellAbsoluteCoordinate.php';
    }

    public function testAbsoluteCoordinateFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(array(Cell::class,'absoluteCoordinate'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerAbsoluteReferences
     */
    public function testAbsoluteReferenceFromString()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'absoluteReference'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerAbsoluteReferences()
    {
        return require 'data/CellAbsoluteReference.php';
    }

    public function testAbsoluteReferenceFromStringWithRangeAddress()
    {
        $cellAddress = 'A1:AI2012';
        try {
            $result = call_user_func(array(Cell::class,'absoluteReference'), $cellAddress);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Cell coordinate string can not be a range of cells');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerSplitRange
     */
    public function testSplitRange()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'splitRange'), $args);
        foreach ($result as $key => $split) {
            if (!is_array($expectedResult[$key])) {
                $this->assertEquals($expectedResult[$key], $split[0]);
            } else {
                $this->assertEquals($expectedResult[$key], $split);
            }
        }
    }

    public function providerSplitRange()
    {
        return require 'data/CellSplitRange.php';
    }

    /**
     * @dataProvider providerBuildRange
     */
    public function testBuildRange()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'buildRange'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerBuildRange()
    {
        return require 'data/CellBuildRange.php';
    }

    public function testBuildRangeInvalid()
    {
        $cellRange = '';
        try {
            $result = call_user_func(array(Cell::class,'buildRange'), $cellRange);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals($e->getMessage(), 'Range does not contain any information');
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @dataProvider providerRangeBoundaries
     */
    public function testRangeBoundaries()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'rangeBoundaries'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRangeBoundaries()
    {
        return require 'data/CellRangeBoundaries.php';
    }

    /**
     * @dataProvider providerRangeDimension
     */
    public function testRangeDimension()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'rangeDimension'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRangeDimension()
    {
        return require 'data/CellRangeDimension.php';
    }

    /**
     * @dataProvider providerGetRangeBoundaries
     */
    public function testGetRangeBoundaries()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'getRangeBoundaries'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerGetRangeBoundaries()
    {
        return require 'data/CellGetRangeBoundaries.php';
    }

    /**
     * @dataProvider providerExtractAllCellReferencesInRange
     */
    public function testExtractAllCellReferencesInRange()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(Cell::class,'extractAllCellReferencesInRange'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerExtractAllCellReferencesInRange()
    {
        return require 'data/CellExtractAllCellReferencesInRange.php';
    }
}
