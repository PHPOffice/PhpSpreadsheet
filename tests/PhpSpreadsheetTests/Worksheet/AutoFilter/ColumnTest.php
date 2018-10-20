<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    private $testInitialColumn = 'H';

    private $testAutoFilterColumnObject;

    private $mockAutoFilterObject;

    public function setUp()
    {
        $this->mockAutoFilterObject = $this->getMockBuilder(AutoFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockAutoFilterObject->expects($this->any())
            ->method('testColumnInRange')
            ->will($this->returnValue(3));

        $this->testAutoFilterColumnObject = new AutoFilter\Column($this->testInitialColumn, $this->mockAutoFilterObject);
    }

    public function testGetColumnIndex()
    {
        $result = $this->testAutoFilterColumnObject->getColumnIndex();
        self::assertEquals($this->testInitialColumn, $result);
    }

    public function testSetColumnIndex()
    {
        $expectedResult = 'L';

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setColumnIndex($expectedResult);
        self::assertInstanceOf(AutoFilter\Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getColumnIndex();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetParent()
    {
        $result = $this->testAutoFilterColumnObject->getParent();
        self::assertInstanceOf(AutoFilter::class, $result);
    }

    public function testSetParent()
    {
        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setParent($this->mockAutoFilterObject);
        self::assertInstanceOf(AutoFilter\Column::class, $result);
    }

    public function testGetFilterType()
    {
        $result = $this->testAutoFilterColumnObject->getFilterType();
        self::assertEquals(AutoFilter\Column::AUTOFILTER_FILTERTYPE_FILTER, $result);
    }

    public function testSetFilterType()
    {
        $result = $this->testAutoFilterColumnObject->setFilterType(AutoFilter\Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
        self::assertInstanceOf(AutoFilter\Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getFilterType();
        self::assertEquals(AutoFilter\Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, $result);
    }

    public function testSetInvalidFilterTypeThrowsException()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $expectedResult = 'Unfiltered';

        $this->testAutoFilterColumnObject->setFilterType($expectedResult);
    }

    public function testGetJoin()
    {
        $result = $this->testAutoFilterColumnObject->getJoin();
        self::assertEquals(AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_OR, $result);
    }

    public function testSetJoin()
    {
        $result = $this->testAutoFilterColumnObject->setJoin(AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND);
        self::assertInstanceOf(AutoFilter\Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getJoin();
        self::assertEquals(AutoFilter\Column::AUTOFILTER_COLUMN_JOIN_AND, $result);
    }

    public function testSetInvalidJoinThrowsException()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $expectedResult = 'Neither';

        $this->testAutoFilterColumnObject->setJoin($expectedResult);
    }

    public function testSetAttributes()
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setAttributes($attributeSet);
        self::assertInstanceOf(AutoFilter\Column::class, $result);
    }

    public function testGetAttributes()
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        $this->testAutoFilterColumnObject->setAttributes($attributeSet);

        $result = $this->testAutoFilterColumnObject->getAttributes();
        self::assertInternalType('array', $result);
        self::assertCount(count($attributeSet), $result);
    }

    public function testSetAttribute()
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        foreach ($attributeSet as $attributeName => $attributeValue) {
            //    Setters return the instance to implement the fluent interface
            $result = $this->testAutoFilterColumnObject->setAttribute($attributeName, $attributeValue);
            self::assertInstanceOf(AutoFilter\Column::class, $result);
        }
    }

    public function testGetAttribute()
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        $this->testAutoFilterColumnObject->setAttributes($attributeSet);

        foreach ($attributeSet as $attributeName => $attributeValue) {
            $result = $this->testAutoFilterColumnObject->getAttribute($attributeName);
            self::assertEquals($attributeValue, $result);
        }
        $result = $this->testAutoFilterColumnObject->getAttribute('nonExistentAttribute');
        self::assertNull($result);
    }

    public function testClone()
    {
        $originalRule = $this->testAutoFilterColumnObject->createRule();
        $result = clone $this->testAutoFilterColumnObject;
        self::assertInstanceOf(AutoFilter\Column::class, $result);
        self::assertCount(1, $result->getRules());
        self::assertContainsOnlyInstancesOf(AutoFilter\Column\Rule::class, $result->getRules());
        $clonedRule = $result->getRules()[0];
        self::assertNotSame($originalRule, $clonedRule);
        self::assertSame($result, $clonedRule->getParent());
    }
}
