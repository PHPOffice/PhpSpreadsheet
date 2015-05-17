<?php

require_once 'testDataFileIterator.php';

class DefaultValueBinderTest extends PHPUnit_Framework_TestCase
{
    protected $cellStub;

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
    }

    protected function createCellStub()
    {
        // Create a stub for the Cell class.
        $this->cellStub = $this->getMockBuilder('PHPExcel_Cell')
            ->disableOriginalConstructor()
            ->getMock();
        // Configure the stub.
        $this->cellStub->expects($this->any())
             ->method('setValueExplicit')
             ->will($this->returnValue(true));

    }

    /**
     * @dataProvider binderProvider
     */
    public function testBindValue($value)
    {
        $this->createCellStub();
        $binder = new PHPExcel_Cell_DefaultValueBinder();
        $result = $binder->bindValue($this->cellStub, $value);
        $this->assertTrue($result);
    }

    public function binderProvider()
    {
        return array(
            array(null),
            array(''),
            array('ABC'),
            array('=SUM(A1:B2)'),
            array(true),
            array(false),
            array(123),
            array(-123.456),
            array('123'),
            array('-123.456'),
            array('#REF!'),
            array(new DateTime()),
        );
    }

    /**
     * @dataProvider providerDataTypeForValue
     */
    public function testDataTypeForValue()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('PHPExcel_Cell_DefaultValueBinder','dataTypeForValue'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDataTypeForValue()
    {
        return new testDataFileIterator('rawTestData/Cell/DefaultValueBinder.data');
    }

    public function testDataTypeForRichTextObject()
    {
        $objRichText = new PHPExcel_RichText();
        $objRichText->createText('Hello World');

        $expectedResult = PHPExcel_Cell_DataType::TYPE_INLINE;
        $result = call_user_func(array('PHPExcel_Cell_DefaultValueBinder','dataTypeForValue'), $objRichText);
        $this->assertEquals($expectedResult, $result);
    }
}
