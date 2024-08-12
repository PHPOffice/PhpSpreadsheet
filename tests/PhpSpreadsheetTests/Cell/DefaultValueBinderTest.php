<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefaultValueBinderTest extends TestCase
{
    /**
     * @dataProvider binderProvider
     */
    public function testBindValue(null|string|bool|int|float|DateTime|DateTimeImmutable $value): void
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

    public function testNonStringableBindValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        try {
            $sheet->getCell('A1')->setValue($this);
            self::fail('Did not receive expected Exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unable to bind unstringable', $e->getMessage());
        }
        $sheet->getCell('A2')->setValue(new StringableObject());
        self::assertSame('abc', $sheet->getCell('A2')->getValue());

        try {
            $sheet->getCell('A3')->setValue(fopen(__FILE__, 'rb'));
            self::fail('Did not receive expected Exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unable to bind unstringable', $e->getMessage());
        }
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerDataTypeForValue
     */
    public function testDataTypeForValue(mixed $expectedResult, mixed $value): void
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

    public function testDataTypeForValueExceptions(): void
    {
        try {
            self::assertSame('s', DefaultValueBinder::dataTypeForValue(new SpreadsheetException()));
        } catch (SpreadsheetException $e) {
            self::fail('Should not have failed for stringable');
        }

        try {
            DefaultValueBinder::dataTypeForValue([]);
            self::fail('Should have failed for array');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('unusable type array', $e->getMessage());
        }

        try {
            DefaultValueBinder::dataTypeForValue(new DateTime());
            self::fail('Should have failed for DateTime');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('unusable type DateTime', $e->getMessage());
        }
    }
}
