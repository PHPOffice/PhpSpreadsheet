<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultValueBinderTest extends TestCase
{
    private function createCellStub(): Cell
    {
        // Create a stub for the Cell class.
        /** @var Cell&MockObject $cellStub */
        $cellStub = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configure the stub.
        $cellStub->expects(self::any())
            ->method('setValueExplicit')
            ->willReturn(true);

        return $cellStub;
    }

    /**
     * @dataProvider binderProvider
     *
     * @param mixed $value
     */
    public function testBindValue($value): void
    {
        $cellStub = $this->createCellStub();
        $binder = new DefaultValueBinder();
        $result = $binder->bindValue($cellStub, $value);
        self::assertTrue($result);
    }

    public function binderProvider(): array
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
            ['123456\n'],
        ];
    }

    /**
     * @dataProvider providerDataTypeForValue
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testDataTypeForValue($expectedResult, $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDataTypeForValue(): array
    {
        return require 'tests/data/Cell/DefaultValueBinder.php';
    }

    public function testDataTypeForRichTextObject(): void
    {
        $objRichText = new RichText();
        $objRichText->createText('Hello World');

        $expectedResult = DataType::TYPE_INLINE;
        $result = DefaultValueBinder::dataTypeForValue($objRichText);
        self::assertEquals($expectedResult, $result);
    }

    public function testCanOverrideStaticMethodWithoutOverridingBindValue(): void
    {
        $cellStub = $this->createCellStub();
        $binder = new ValueBinderWithOverriddenDataTypeForValue();

        self::assertFalse($binder::$called);
        $binder->bindValue($cellStub, 123);
        self::assertTrue($binder::$called);
    }
}
