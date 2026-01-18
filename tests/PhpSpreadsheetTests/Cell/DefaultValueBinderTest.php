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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stringable;

/**
 * Tests for DefaultValueBinder::dataTypeForValue().
 */
class DefaultValueBinderTest extends TestCase
{
    /**
     * Test data type detection for various value types.
     */
    #[DataProvider('binderProvider')]
    public function testBindValue(mixed $value, string $expectedDataType, mixed $expectedValue = null): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell = $sheet->getCell('A1');
        $binder = new DefaultValueBinder();
        self::assertTrue($binder->bindValue($cell, $value));
        $result = $cell->getDataType();
        self::assertSame($expectedDataType, $result);
        self::assertSame($expectedValue ?? $value, $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @return array<string, array{0: mixed, 1:string, 2?: mixed}>
     */
    public static function binderProvider(): array
    {
        $richText = new RichText();
        $richText->createTextRun('Hello World');
        $stringableFormula = new class () implements Stringable {
            public function __toString(): string
            {
                return '=SUM(A1:A10)';
            }
        };

        return [
            // Null
            'null' => [null, DataType::TYPE_NULL],

            // Booleans
            'true' => [true, DataType::TYPE_BOOL],
            'false' => [false, DataType::TYPE_BOOL],
            'bool-like string' => ['false', DataType::TYPE_STRING],

            // Integers
            'integer zero' => [0, DataType::TYPE_NUMERIC],
            'positive integer' => [42, DataType::TYPE_NUMERIC],
            'negative integer' => [-100, DataType::TYPE_NUMERIC],
            'trailing newline ignored, probably an error' => ["123456\n", DataType::TYPE_NUMERIC, 123456],
            'trailing space' => ['123456 ', DataType::TYPE_STRING],

            // Very large integers treated as string to prevent precision loss
            'large positive' => [1_000_000_000_000_000, DataType::TYPE_STRING, '1000000000000000'],
            'large negative' => [-1_000_000_000_000_000, DataType::TYPE_STRING, '-1000000000000000'],

            // Floats
            'float' => [3.14159, DataType::TYPE_NUMERIC],
            'negative float' => [-2.5, DataType::TYPE_NUMERIC],
            'float zero' => [0.0, DataType::TYPE_NUMERIC],

            // Strings
            'empty string' => ['', DataType::TYPE_STRING],
            'simple text' => ['Hello World', DataType::TYPE_STRING],
            'text with numbers' => ['ABC123', DataType::TYPE_STRING],
            'leading zero number' => ['007', DataType::TYPE_STRING],

            // Numeric strings
            'numeric string integer' => ['123', DataType::TYPE_NUMERIC, 123],
            'numeric string float' => ['123.25', DataType::TYPE_NUMERIC, 123.25],
            'scientific notation 1' => ['1.5e10', DataType::TYPE_NUMERIC, 1.5E10],
            'issue 4766 very large positive exponent' => ['4E433', DataType::TYPE_STRING],
            'issue 4766 scientific large negative exponent' => ['6E-444', DataType::TYPE_NUMERIC, 0.0],
            'issue 4766 small exponent no decimal point numeric' => ['4E4', DataType::TYPE_NUMERIC, 4E4],
            'negative numeric string' => ['-42.5', DataType::TYPE_NUMERIC, -42.5],

            // Valid formulas
            'formula simple' => ['=1+1', DataType::TYPE_FORMULA],
            'formula sum' => ['=SUM(A1:A10)', DataType::TYPE_FORMULA],
            'formula cell ref' => ['=A1', DataType::TYPE_FORMULA],
            'formula if' => ['=IF(A1>0,1,0)', DataType::TYPE_FORMULA],
            'formula vlookup' => ['=VLOOKUP(A1,B1:C10,2,FALSE)', DataType::TYPE_FORMULA],

            // Invalid formulas (treated as strings)
            'equals only' => ['=', DataType::TYPE_STRING],
            'equals with space' => ['= ', DataType::TYPE_STRING],
            'Issue 1310 Multiple = at start' => ['======', DataType::TYPE_STRING],
            'Issue 1310 Variant 1' => ['= =====', DataType::TYPE_STRING],
            'Issue 1310 Variant 2' => ['=2*3=', DataType::TYPE_STRING],

            // Error codes
            'error NULL' => ['#NULL!', DataType::TYPE_ERROR],
            'error DIV0' => ['#DIV/0!', DataType::TYPE_ERROR],
            'error VALUE' => ['#VALUE!', DataType::TYPE_ERROR],
            'error REF' => ['#REF!', DataType::TYPE_ERROR],
            'error NAME' => ['#NAME?', DataType::TYPE_ERROR],
            'error NUM' => ['#NUM!', DataType::TYPE_ERROR],
            'error NA' => ['#N/A', DataType::TYPE_ERROR],

            // DateTime strings should be treated as string
            'datetime format' => ['2024-01-15 10:30:00', DataType::TYPE_STRING],
            'date only' => ['2024-01-15', DataType::TYPE_STRING],
            'time only' => ['10:30:00', DataType::TYPE_STRING],

            // DateTime objects should be treated as string
            'DateTime' => [new DateTime('Jan 1, 2000'), DataType::TYPE_STRING, '2000-01-01 00:00:00'],
            'DateTimeImmutable' => [new DateTimeImmutable('Jan 2, 2000'), DataType::TYPE_STRING, '2000-01-02 00:00:00'],

            // Stringable object
            'Stringable object' => [new StringableObject(), DataType::TYPE_STRING, 'abc'],
            'Stringable formula' => [$stringableFormula, DataType::TYPE_FORMULA, '=SUM(A1:A10)'],

            // Rich Text should return inline
            'Rich Text' => [$richText, DataType::TYPE_INLINE],
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

        try {
            $sheet->getCell('A3')->setValue(fopen(__FILE__, 'rb'));
            self::fail('Did not receive expected Exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unable to bind unstringable', $e->getMessage());
        }

        try {
            $sheet->getCell('A3')->setValue([]);
            self::fail('Did not receive expected Exception');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unable to bind unstringable', $e->getMessage());
        }

        $spreadsheet->disconnectWorksheets();
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

    /**
     * Test that calling dataTypeForValue multiple times with formulas
     * works correctly (verifies singleton reuse doesn't cause issues).
     */
    public function testMultipleFormulaCallsWorkCorrectly(): void
    {
        $result1 = DefaultValueBinder::dataTypeForValue('=SUM(A1:A10)');
        $result2 = DefaultValueBinder::dataTypeForValue('=AVERAGE(B1:B10)');
        $result3 = DefaultValueBinder::dataTypeForValue('=1+2+3');

        self::assertSame(DataType::TYPE_FORMULA, $result1);
        self::assertSame(DataType::TYPE_FORMULA, $result2);
        self::assertSame(DataType::TYPE_FORMULA, $result3);
    }

    /**
     * Test mixed calls to ensure state is properly preserved.
     */
    public function testMixedCallsPreserveCorrectBehavior(): void
    {
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=A1'));
        self::assertSame(DataType::TYPE_STRING, DefaultValueBinder::dataTypeForValue('Hello'));
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=B2'));
        self::assertSame(DataType::TYPE_NUMERIC, DefaultValueBinder::dataTypeForValue(42));
        self::assertSame(DataType::TYPE_FORMULA, DefaultValueBinder::dataTypeForValue('=C3*D4'));
    }
}
