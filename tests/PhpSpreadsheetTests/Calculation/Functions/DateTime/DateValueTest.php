<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateValue;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class DateValueTest extends TestCase
{
    private int $excelCalendar;

    private string $returnDateType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->excelCalendar = SharedDate::getExcelCalendar();
        $this->returnDateType = Functions::getReturnDateType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        SharedDate::setExcelCalendar($this->excelCalendar);
        Functions::setReturnDateType($this->returnDateType);
    }

    private function expectationIsTemplate(mixed $expectedResult): bool
    {
        return is_string($expectedResult) && str_starts_with($expectedResult, 'Y-');
    }

    private function parseTemplatedExpectation(float|int|string $expectedResult): string
    {
        /** @var float */
        $x = DateValue::fromString(
            (new DateTimeImmutable(
                str_replace('Y', (new DateTimeImmutable('now'))->format('Y'), (string) $expectedResult)
            ))->format('Y-m-d')
        );

        return (string) $x;
    }

    /**
     * @dataProvider providerDATEVALUE
     */
    public function testDirectCallToDATEVALUE(int|string $expectedResult, bool|int|string $value): void
    {
        if ($this->expectationIsTemplate($expectedResult)) {
            $expectedResult = $this->parseTemplatedExpectation((string) $expectedResult);
        }

        $result = DateValue::fromString($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerDATEVALUE
     */
    public function testDATEVALUEAsFormula(float|int|string $expectedResult, mixed ...$args): void
    {
        if ($this->expectationIsTemplate($expectedResult)) {
            $expectedResult = $this->parseTemplatedExpectation($expectedResult);
        }

        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DATEVALUE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerDATEVALUE
     */
    public function testDATEVALUEInWorksheet(float|int|string $expectedResult, mixed ...$args): void
    {
        if ($this->expectationIsTemplate($expectedResult)) {
            $expectedResult = $this->parseTemplatedExpectation($expectedResult);
        }

        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATEVALUE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDATEVALUE(): array
    {
        return require 'tests/data/Calculation/DateTime/DATEVALUE.php';
    }

    public function testRefArgNull(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=DATEVALUE(B1)');
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerUnhappyDATEVALUE
     */
    public function testDATEVALUEUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATEVALUE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDATEVALUE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DATEVALUE() function'],
        ];
    }

    public function testDATEVALUEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateValue::fromString('2012-1-31');
        self::assertEquals(1327968000, $result);
        self::assertEqualsWithDelta(1327968000, $result, 1E-8);
    }

    public function testDATEVALUEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateValue::fromString('2012-1-31');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, DateTimeInterface::class));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEVALUEWith1904Calendar(): void
    {
        SharedDate::setExcelCalendar(SharedDate::CALENDAR_MAC_1904);

        self::assertEquals(5428, DateValue::fromString('1918-11-11'));
        self::assertEquals(0, DateValue::fromString('1904-01-01'));
        self::assertEquals('#VALUE!', DateValue::fromString('1903-12-31'));
        self::assertEquals('#VALUE!', DateValue::fromString('1900-02-29'));
    }

    /**
     * @dataProvider providerDateValueArray
     */
    public function testDateValueArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DATEVALUE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDateValueArray(): array
    {
        return [
            'row vector' => [[[44562, 44724, 45129]], '{"2022-01-01", "2022-06-12", "2023-07-22"}'],
            'column vector' => [[[44562], [44564], [44567]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}'],
            'matrix' => [[[44562, 44571], [44788, 44926]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}'],
        ];
    }
}
