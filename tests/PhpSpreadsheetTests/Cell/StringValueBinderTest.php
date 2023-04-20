<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StringValueBinderTest extends TestCase
{
    /**
     * @var IValueBinder
     */
    private $valueBinder;

    protected function setUp(): void
    {
        $this->valueBinder = Cell::getValueBinder();
    }

    protected function tearDown(): void
    {
        Cell::setValueBinder($this->valueBinder);
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
        string $expectedDataType
    ): void {
        Cell::setValueBinder(new StringValueBinder());
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesDefault(): array
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
            ['=SUM(A1:C3)', '=SUM(A1:C3)', DataType::TYPE_STRING],
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
        string $expectedDataType
    ): void {
        $binder = new StringValueBinder();
        $binder->setNullConversion(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesSuppressNullConversion(): array
    {
        return [
            [null, null, DataType::TYPE_NULL],
            [true, '1', DataType::TYPE_STRING],
            [123, '123', DataType::TYPE_STRING],
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
        string $expectedDataType
    ): void {
        $binder = new StringValueBinder();
        $binder->setBooleanConversion(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesSuppressBooleanConversion(): array
    {
        return [
            [true, true, DataType::TYPE_BOOL],
            [false, false, DataType::TYPE_BOOL],
            [null, '', DataType::TYPE_STRING],
            [123, '123', DataType::TYPE_STRING],
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
        string $expectedDataType
    ): void {
        $binder = new StringValueBinder();
        $binder->setNumericConversion(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesSuppressNumericConversion(): array
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
            [true, '1', DataType::TYPE_STRING],
            [false, '', DataType::TYPE_STRING],
            [null, '', DataType::TYPE_STRING],
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
        string $expectedDataType
    ): void {
        $binder = new StringValueBinder();
        $binder->setFormulaConversion(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesSuppressFormulaConversion(): array
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
        string $expectedDataType
    ): void {
        $binder = new StringValueBinder();
        $binder->setConversionForAllValueTypes(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($value);
        self::assertSame($expectedValue, $cell->getValue());
        self::assertSame($expectedDataType, $cell->getDataType());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDataValuesSuppressAllConversion(): array
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

        $binder = new StringValueBinder();
        $binder->setConversionForAllValueTypes(false);
        Cell::setValueBinder($binder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $cell->setValue($objRichText);
        self::assertSame('inlineStr', $cell->getDataType());
        self::assertSame('Hello World', $cell->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
