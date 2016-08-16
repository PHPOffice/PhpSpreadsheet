<?php

namespace PhpSpreadsheet\Tests\Cell;

use PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpSpreadsheet\Cell;
use PhpSpreadsheet\RichText;
use PhpSpreadsheet\Cell\DataType;

class DefaultValueBinderTest extends \PHPUnit_Framework_TestCase
{
    protected $cellStub;

    public function setUp()
    {
        if (!defined('PHPSPREADSHEET_ROOT')) {
            define('PHPSPREADSHEET_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPSPREADSHEET_ROOT . '/Bootstrap.php');
    }

    protected function createCellStub()
    {
        // Create a stub for the Cell class.
        $this->cellStub = $this->getMockBuilder(Cell::class)
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
        $binder = new DefaultValueBinder();
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
            array(new \DateTime()),
        );
    }

    /**
     * @dataProvider providerDataTypeForValue
     */
    public function testDataTypeForValue()
    {
        list($args, $expectedResult) = func_get_args();
        $result = call_user_func_array(array(DefaultValueBinder::class,'dataTypeForValue'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDataTypeForValue()
    {
        return require 'data/Cell/DefaultValueBinder.php';
    }

    public function testDataTypeForRichTextObject()
    {
        $objRichText = new RichText();
        $objRichText->createText('Hello World');

        $expectedResult = DataType::TYPE_INLINE;
        $result = call_user_func(array(DefaultValueBinder::class,'dataTypeForValue'), $objRichText);
        $this->assertEquals($expectedResult, $result);
    }
}
