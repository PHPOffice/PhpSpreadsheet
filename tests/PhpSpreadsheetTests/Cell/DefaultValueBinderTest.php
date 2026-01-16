<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
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
    public function testBindValue(mixed $value, string $expectedDataType): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame($expectedDataType, $result);
    }

    /**
     * @return array<string, array{mixed, string}>
     */
    public static function binderProvider(): array
    {
        return [
            // Null
            'null' => [null, DataType::TYPE_NULL],

            // Booleans
            'true' => [true, DataType::TYPE_BOOL],
            'false' => [false, DataType::TYPE_BOOL],

            // Integers
            'integer zero' => [0, DataType::TYPE_NUMERIC],
            'positive integer' => [42, DataType::TYPE_NUMERIC],
            'negative integer' => [-100, DataType::TYPE_NUMERIC],

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
            'numeric string integer' => ['123', DataType::TYPE_NUMERIC],
            'numeric string float' => ['123.45', DataType::TYPE_NUMERIC],
            'scientific notation' => ['1.5e10', DataType::TYPE_NUMERIC],
            'negative numeric string' => ['-42.5', DataType::TYPE_NUMERIC],

            // Valid formulas
            'formula simple' => ['=1+1', DataType::TYPE_FORMULA],
            'formula sum' => ['=SUM(A1:A10)', DataType::TYPE_FORMULA],
            'formula cell ref' => ['=A1', DataType::TYPE_FORMULA],
            'formula if' => ['=IF(A1>0,1,0)', DataType::TYPE_FORMULA],
            'formula vlookup' => ['=VLOOKUP(A1,B1:C10,2,FALSE)', DataType::TYPE_FORMULA],

            // Invalid formulas (treated as strings)
            'equals only' => ['=', DataType::TYPE_STRING],
            'equals with space' => ['= ', DataType::TYPE_STRING],

            // Error codes
            'error NULL' => ['#NULL!', DataType::TYPE_ERROR],
            'error DIV0' => ['#DIV/0!', DataType::TYPE_ERROR],
            'error VALUE' => ['#VALUE!', DataType::TYPE_ERROR],
            'error REF' => ['#REF!', DataType::TYPE_ERROR],
            'error NAME' => ['#NAME?', DataType::TYPE_ERROR],
            'error NUM' => ['#NUM!', DataType::TYPE_ERROR],
            'error NA' => ['#N/A', DataType::TYPE_ERROR],
        ];
    }

    /**
     * Test that RichText values return TYPE_INLINE.
     */
    public function testRichTextReturnsTypeInline(): void
    {
        $richText = new RichText();
        $richText->createTextRun('Hello World');

        $result = DefaultValueBinder::dataTypeForValue($richText);
        self::assertSame(DataType::TYPE_INLINE, $result);
    }

    /**
     * Test that DateTime objects are handled correctly.
     * Note: DateTime values are converted to string format in bindValue(),
     * so dataTypeForValue() would receive a string after conversion.
     */
    #[DataProvider('dateTimeStringProvider')]
    public function testDateTimeStringReturnsTypeString(string $dateTimeString): void
    {
        $result = DefaultValueBinder::dataTypeForValue($dateTimeString);
        self::assertSame(DataType::TYPE_STRING, $result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function dateTimeStringProvider(): array
    {
        return [
            'datetime format' => ['2024-01-15 10:30:00'],
            'date only' => ['2024-01-15'],
            'time only' => ['10:30:00'],
        ];
    }

    /**
     * Test Stringable objects are converted and handled as strings.
     */
    public function testStringableReturnsTypeString(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'Hello from Stringable';
            }
        };

        $result = DefaultValueBinder::dataTypeForValue($stringable);
        self::assertSame(DataType::TYPE_STRING, $result);
    }

    /**
     * Test Stringable that returns a formula string.
     */
    public function testStringableFormulaReturnsTypeFormula(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '=SUM(A1:A10)';
            }
        };

        $result = DefaultValueBinder::dataTypeForValue($stringable);
        self::assertSame(DataType::TYPE_FORMULA, $result);
    }

    /**
     * Test very large integers are treated as strings to prevent precision loss.
     */
    #[DataProvider('largeIntegerProvider')]
    public function testLargeIntegersReturnTypeString(int $value): void
    {
        $result = DefaultValueBinder::dataTypeForValue($value);
        self::assertSame(DataType::TYPE_STRING, $result);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function largeIntegerProvider(): array
    {
        return [
            'large positive' => [1_000_000_000_000_000],
            'large negative' => [-1_000_000_000_000_000],
        ];
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
