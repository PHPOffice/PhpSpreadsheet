<?php

namespace PhpSpreadsheet\Tests\Worksheet\AutoFilter\Column;

use PHPExcel\Worksheet\AutoFilter\Column;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    private $testAutoFilterRuleObject;

    private $mockAutoFilterColumnObject;

    public function setUp()
    {
        $this->mockAutoFilterColumnObject = $this->getMockBuilder(Column::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockAutoFilterColumnObject->expects($this->any())
            ->method('testColumnInRange')
            ->will($this->returnValue(3));

        $this->testAutoFilterRuleObject = new Column\Rule(
            $this->mockAutoFilterColumnObject
        );
    }

    public function testGetRuleType()
    {
        $result = $this->testAutoFilterRuleObject->getRuleType();
        $this->assertEquals(Column\Rule::AUTOFILTER_RULETYPE_FILTER, $result);
    }

    public function testSetRuleType()
    {
        $expectedResult = Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP;

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterRuleObject->setRuleType($expectedResult);
        $this->assertInstanceOf(Column\Rule::class, $result);

        $result = $this->testAutoFilterRuleObject->getRuleType();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetValue()
    {
        $expectedResult = 100;

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterRuleObject->setValue($expectedResult);
        $this->assertInstanceOf(Column\Rule::class, $result);

        $result = $this->testAutoFilterRuleObject->getValue();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetOperator()
    {
        $result = $this->testAutoFilterRuleObject->getOperator();
        $this->assertEquals(Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL, $result);
    }

    public function testSetOperator()
    {
        $expectedResult = Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN;

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterRuleObject->setOperator($expectedResult);
        $this->assertInstanceOf(Column\Rule::class, $result);

        $result = $this->testAutoFilterRuleObject->getOperator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetGrouping()
    {
        $expectedResult = Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH;

        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterRuleObject->setGrouping($expectedResult);
        $this->assertInstanceOf(Column\Rule::class, $result);

        $result = $this->testAutoFilterRuleObject->getGrouping();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetParent()
    {
        $result = $this->testAutoFilterRuleObject->getParent();
        $this->assertInstanceOf(Column::class, $result);
    }

    public function testSetParent()
    {
        //    Setters return the instance to implement the fluent interface
        $result = $this->testAutoFilterRuleObject->setParent($this->mockAutoFilterColumnObject);
        $this->assertInstanceOf(Column\Rule::class, $result);
    }

    public function testClone()
    {
        $result = clone $this->testAutoFilterRuleObject;
        $this->assertInstanceOf(Column\Rule::class, $result);
    }
}
