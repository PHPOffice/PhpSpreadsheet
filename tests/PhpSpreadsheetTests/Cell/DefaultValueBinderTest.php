<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText;

class DefaultValueBinderTest extends \PHPUnit_Framework_TestCase
{
    protected $cellStub;

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
     *
     * @param mixed $value
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
        return [
            [null],
            [''],
            ['ABC'],
            ['=SUM(A1:B2)'],
            [true],
            [false],
            [123],
            [-123.456],
            ['123'],
            ['-123.456'],
            ['#REF!'],
            [new \DateTime()],
        ];
    }

    /**
     * @dataProvider providerDataTypeForValue
     *
     * @param mixed $expectedResult
     */
    public function testDataTypeForValue($expectedResult, ...$args)
    {
        $result = DefaultValueBinder::dataTypeForValue(...$args);
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
        $result = DefaultValueBinder::dataTypeForValue($objRichText);
        $this->assertEquals($expectedResult, $result);
    }
}
