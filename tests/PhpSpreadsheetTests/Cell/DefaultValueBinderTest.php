<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefaultValueBinderTest extends TestCase
{
    /**
     * @dataProvider binderProvider
     *
     * @param mixed $value
     */
    public function testBindValue($value): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $binder = new DefaultValueBinder();
        $result = $binder->bindValue($cell, $value);
        self::assertTrue($result);
        $spreadsheet->disconnectWorksheets();
    }

    public static function binderProvider(): array
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

    public static function providerDataTypeForValue(): array
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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $binder = new ValueBinderWithOverriddenDataTypeForValue();

        self::assertFalse($binder::$called);
        $binder->bindValue($cell, 123);
        self::assertTrue($binder::$called);
        $spreadsheet->disconnectWorksheets();
    }
}
