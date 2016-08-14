<?php

namespace PhpSpreadsheet\Tests\Worksheet\AutoFilter\Column;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    private $_testAutoFilterRuleObject;

    private $_mockAutoFilterColumnObject;

    public function setUp()
    {
        $this->_mockAutoFilterColumnObject = $this->getMockBuilder('\PHPExcel\Worksheet\AutoFilter\Column')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAutoFilterColumnObject->expects($this->any())
            ->method('testColumnInRange')
            ->will($this->returnValue(3));

        $this->_testAutoFilterRuleObject = new \PHPExcel\Worksheet\AutoFilter\Column\Rule(
            $this->_mockAutoFilterColumnObject
        );
    }

    public function testGetRuleType()
    {
        $result = $this->_testAutoFilterRuleObject->getRuleType();
        $this->assertEquals(\PHPExcel\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_FILTER, $result);
    }

    public function testSetRuleType()
    {
        $expectedResult = \PHPExcel\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP;

        //    Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setRuleType($expectedResult);
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getRuleType();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetValue()
    {
        $expectedResult = 100;

        //    Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setValue($expectedResult);
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getValue();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetOperator()
    {
        $result = $this->_testAutoFilterRuleObject->getOperator();
        $this->assertEquals(\PHPExcel\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_EQUAL, $result);
    }

    public function testSetOperator()
    {
        $expectedResult = \PHPExcel\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN;

        //    Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setOperator($expectedResult);
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getOperator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetGrouping()
    {
        $expectedResult = \PHPExcel\Worksheet\AutoFilter\Column\Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH;

        //    Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setGrouping($expectedResult);
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);

        $result = $this->_testAutoFilterRuleObject->getGrouping();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetParent()
    {
        $result = $this->_testAutoFilterRuleObject->getParent();
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column', $result);
    }

    public function testSetParent()
    {
        //    Setters return the instance to implement the fluent interface
        $result = $this->_testAutoFilterRuleObject->setParent($this->_mockAutoFilterColumnObject);
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);
    }

    public function testClone()
    {
        $result = clone $this->_testAutoFilterRuleObject;
        $this->assertInstanceOf('\PHPExcel\Worksheet\AutoFilter\Column\Rule', $result);
    }
}
