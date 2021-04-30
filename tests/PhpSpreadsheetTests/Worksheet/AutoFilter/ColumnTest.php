<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @var string
     */
    private $testInitialColumn = 'H';

    /**
     * @var Column
     */
    private $testAutoFilterColumnObject;

    /**
     * @var AutoFilter&MockObject
     */
    private $mockAutoFilterObject;

    protected function setUp(): void
    {
        $this->mockAutoFilterObject = $this->getMockBuilder(AutoFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockAutoFilterObject->expects(self::any())
            ->method('testColumnInRange')
            ->willReturn(3);

        $this->testAutoFilterColumnObject = new Column($this->testInitialColumn, $this->mockAutoFilterObject);
    }

    public function testGetColumnIndex(): void
    {
        $result = $this->testAutoFilterColumnObject->getColumnIndex();
        self::assertEquals($this->testInitialColumn, $result);
    }

    public function testSetColumnIndex(): void
    {
        $expectedResult = 'L';

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setColumnIndex($expectedResult);
        self::assertInstanceOf(Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getColumnIndex();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetParent(): void
    {
        $result = $this->testAutoFilterColumnObject->getParent();
        self::assertInstanceOf(AutoFilter::class, $result);
    }

    public function testSetParent(): void
    {
        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setParent($this->mockAutoFilterObject);
        self::assertInstanceOf(Column::class, $result);
    }

    public function testGetFilterType(): void
    {
        $result = $this->testAutoFilterColumnObject->getFilterType();
        self::assertEquals(Column::AUTOFILTER_FILTERTYPE_FILTER, $result);
    }

    public function testSetFilterType(): void
    {
        $result = $this->testAutoFilterColumnObject->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
        self::assertInstanceOf(Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getFilterType();
        self::assertEquals(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, $result);
    }

    public function testSetInvalidFilterTypeThrowsException(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $expectedResult = 'Unfiltered';

        $this->testAutoFilterColumnObject->setFilterType($expectedResult);
    }

    public function testGetJoin(): void
    {
        $result = $this->testAutoFilterColumnObject->getJoin();
        self::assertEquals(Column::AUTOFILTER_COLUMN_JOIN_OR, $result);
    }

    public function testSetJoin(): void
    {
        $result = $this->testAutoFilterColumnObject->setJoin(Column::AUTOFILTER_COLUMN_JOIN_AND);
        self::assertInstanceOf(Column::class, $result);

        $result = $this->testAutoFilterColumnObject->getJoin();
        self::assertEquals(Column::AUTOFILTER_COLUMN_JOIN_AND, $result);
    }

    public function testSetInvalidJoinThrowsException(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $expectedResult = 'Neither';

        $this->testAutoFilterColumnObject->setJoin($expectedResult);
    }

    public function testSetAttributes(): void
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterColumnObject->setAttributes($attributeSet);
        self::assertInstanceOf(Column::class, $result);
    }

    public function testGetAttributes(): void
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        $this->testAutoFilterColumnObject->setAttributes($attributeSet);

        $result = $this->testAutoFilterColumnObject->getAttributes();
        self::assertIsArray($result);
        self::assertCount(count($attributeSet), $result);
    }

    public function testSetAttribute(): void
    {
        $attributeSet = [
            'val' => 100,
            'maxVal' => 200,
        ];

        foreach ($attributeSet as $attributeName => $attributeValue) {
            //    Setters return the instance to implement the fluent interface
            $result = $this->testAutoFilterColumnObject->setAttribute($attributeName, $attributeValue);
            self::assertInstanceOf(Column::class, $result);
        }
    }

    public function testGetAttribute(): void
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

    public function testClone(): void
    {
        $originalRule = $this->testAutoFilterColumnObject->createRule();
        $result = clone $this->testAutoFilterColumnObject;
        self::assertInstanceOf(Column::class, $result);
        self::assertCount(1, $result->getRules());
        self::assertContainsOnlyInstancesOf(AutoFilter\Column\Rule::class, $result->getRules());
        $clonedRule = $result->getRules()[0];
        self::assertNotSame($originalRule, $clonedRule);
        self::assertSame($result, $clonedRule->getParent());
    }
}
