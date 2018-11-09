<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;

class DefaultValueBinderTest extends TestCase
{
    private function createCellStub()
    {
        // Create a stub for the Cell class.
        /** @var Cell $cellStub */
        $cellStub = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configure the stub.
        $cellStub->expects($this->any())
            ->method('setValueExplicit')
            ->will($this->returnValue(true));

        return $cellStub;
    }

    /**
     * @dataProvider binderProvider
     *
     * @param mixed $value
     */
    public function testBindValue($value)
    {
        $cellStub = $this->createCellStub();
        $binder = new DefaultValueBinder();
        $result = $binder->bindValue($cellStub, $value);
        self::assertTrue($result);
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
            [new DateTime()],
            [new DateTimeImmutable()],
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
        self::assertEquals($expectedResult, $result);
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
        self::assertEquals($expectedResult, $result);
    }

    public function testCanOverrideStaticMethodWithoutOverridingBindValue()
    {
        $cellStub = $this->createCellStub();
        $binder = new ValueBinderWithOverriddenDataTypeForValue();

        self::assertFalse($binder::$called);
        $binder->bindValue($cellStub, 123);
        self::assertTrue($binder::$called);
    }
}
