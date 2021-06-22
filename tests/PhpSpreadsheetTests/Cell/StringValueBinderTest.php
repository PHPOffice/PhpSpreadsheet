<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StringValueBinderTest extends TestCase
{
    /**
     * @param mixed $expectedValue
     *
     * @return Cell&MockObject
     */
    protected function createCellStub($expectedValue, string $expectedDataType, bool $quotePrefix = false): MockObject
    {
        /** @var Style&MockObject $styleStub */
        $styleStub = $this->getMockBuilder(Style::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Cell&MockObject $cellStub */
        $cellStub = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configure the stub.
        $cellStub->expects(self::once())
            ->method('setValueExplicit')
            ->with($expectedValue, $expectedDataType)
            ->willReturn(true);
        $cellStub->expects($quotePrefix ? self::once() : self::never())
            ->method('getStyle')
            ->willReturn($styleStub);

        return $cellStub;
    }

    /**
     * @dataProvider providerDataValuesDefault
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderDefaultBehaviour(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesDefault(): array
    {
        return [
            [null, '', DataType::TYPE_STRING],
            [true, '1', DataType::TYPE_STRING],
            [false, '', DataType::TYPE_STRING],
            ['', '', DataType::TYPE_STRING],
            ['123', '123', DataType::TYPE_STRING],
            ['123.456', '123.456', DataType::TYPE_STRING],
            ['0.123', '0.123', DataType::TYPE_STRING],
            ['.123', '.123', DataType::TYPE_STRING],
            ['-0.123', '-0.123', DataType::TYPE_STRING],
            ['-.123', '-.123', DataType::TYPE_STRING],
            ['1.23e-4', '1.23e-4', DataType::TYPE_STRING],
            ['ABC', 'ABC', DataType::TYPE_STRING],
            ['=SUM(A1:C3)', '=SUM(A1:C3)', DataType::TYPE_STRING, true],
            [123, '123', DataType::TYPE_STRING],
            [123.456, '123.456', DataType::TYPE_STRING],
            [0.123, '0.123', DataType::TYPE_STRING],
            [.123, '0.123', DataType::TYPE_STRING],
            [-0.123, '-0.123', DataType::TYPE_STRING],
            [-.123, '-0.123', DataType::TYPE_STRING],
            [1.23e-4, '0.000123', DataType::TYPE_STRING],
            [1.23e-24, '1.23E-24', DataType::TYPE_STRING],
            [new DateTime('2021-06-01 00:00:00', new DateTimeZone('UTC')), '2021-06-01 00:00:00', DataType::TYPE_STRING],
        ];
    }

    /**
     * @dataProvider providerDataValuesSuppressNullConversion
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderSuppressNullConversion(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->setNullConversion(false);
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesSuppressNullConversion(): array
    {
        return [
            [null, null, DataType::TYPE_NULL],
        ];
    }

    /**
     * @dataProvider providerDataValuesSuppressBooleanConversion
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderSuppressBooleanConversion(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->setBooleanConversion(false);
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesSuppressBooleanConversion(): array
    {
        return [
            [true, true, DataType::TYPE_BOOL],
            [false, false, DataType::TYPE_BOOL],
        ];
    }

    /**
     * @dataProvider providerDataValuesSuppressNumericConversion
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderSuppressNumericConversion(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->setNumericConversion(false);
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesSuppressNumericConversion(): array
    {
        return [
            [123, 123, DataType::TYPE_NUMERIC],
            [123.456, 123.456, DataType::TYPE_NUMERIC],
            [0.123, 0.123, DataType::TYPE_NUMERIC],
            [.123, 0.123, DataType::TYPE_NUMERIC],
            [-0.123, -0.123, DataType::TYPE_NUMERIC],
            [-.123, -0.123, DataType::TYPE_NUMERIC],
            [1.23e-4, 0.000123, DataType::TYPE_NUMERIC],
            [1.23e-24, 1.23E-24, DataType::TYPE_NUMERIC],
        ];
    }

    /**
     * @dataProvider providerDataValuesSuppressFormulaConversion
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderSuppressFormulaConversion(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->setFormulaConversion(false);
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesSuppressFormulaConversion(): array
    {
        return [
            ['=SUM(A1:C3)', '=SUM(A1:C3)', DataType::TYPE_FORMULA, false],
        ];
    }

    /**
     * @dataProvider providerDataValuesSuppressAllConversion
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testStringValueBinderSuppressAllConversion(
        $value,
        $expectedValue,
        string $expectedDataType,
        bool $quotePrefix = false
    ): void {
        $cellStub = $this->createCellStub($expectedValue, $expectedDataType, $quotePrefix);

        $binder = new StringValueBinder();
        $binder->setConversionForAllValueTypes(false);
        $binder->bindValue($cellStub, $value);
    }

    public function providerDataValuesSuppressAllConversion(): array
    {
        return [
            [null, null, DataType::TYPE_NULL],
            [true, true, DataType::TYPE_BOOL],
            [false, false, DataType::TYPE_BOOL],
            ['', '', DataType::TYPE_STRING],
            ['123', '123', DataType::TYPE_STRING],
            ['123.456', '123.456', DataType::TYPE_STRING],
            ['0.123', '0.123', DataType::TYPE_STRING],
            ['.123', '.123', DataType::TYPE_STRING],
            ['-0.123', '-0.123', DataType::TYPE_STRING],
            ['-.123', '-.123', DataType::TYPE_STRING],
            ['1.23e-4', '1.23e-4', DataType::TYPE_STRING],
            ['ABC', 'ABC', DataType::TYPE_STRING],
            ['=SUM(A1:C3)', '=SUM(A1:C3)', DataType::TYPE_FORMULA, false],
            [123, 123, DataType::TYPE_NUMERIC],
            [123.456, 123.456, DataType::TYPE_NUMERIC],
            [0.123, 0.123, DataType::TYPE_NUMERIC],
            [.123, 0.123, DataType::TYPE_NUMERIC],
            [-0.123, -0.123, DataType::TYPE_NUMERIC],
            [-.123, -0.123, DataType::TYPE_NUMERIC],
            [1.23e-4, 0.000123, DataType::TYPE_NUMERIC],
            [1.23e-24, 1.23E-24, DataType::TYPE_NUMERIC],
        ];
    }

    public function testStringValueBinderForRichTextObject(): void
    {
        $objRichText = new RichText();
        $objRichText->createText('Hello World');

        $cellStub = $this->createCellStub($objRichText, DataType::TYPE_INLINE);

        $binder = new StringValueBinder();
        $binder->setConversionForAllValueTypes(false);
        $binder->bindValue($cellStub, $objRichText);
    }
}
