<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeValue;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class TimeValueTest extends TestCase
{
    private string $returnDateType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->returnDateType = Functions::getReturnDateType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Functions::setReturnDateType($this->returnDateType);
    }

    /**
     * @dataProvider providerTIMEVALUE
     */
    public function testDirectCallToTIMEVALUE(int|float|string $expectedResult, bool|int|string $value): void
    {
        $result = TimeValue::fromString($value);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerTIMEVALUE
     */
    public function testTIMEVALUEAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=TIMEVALUE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);
    }

    /**
     * @dataProvider providerTIMEVALUE
     */
    public function testTIMEVALUEInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=TIMEVALUE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-8);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerTIMEVALUE(): array
    {
        return require 'tests/data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testRefArgNull(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=TIMEVALUE(B1)');
        self::assertSame('#VALUE!', $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTIMEVALUEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = TimeValue::fromString('7:30:20');
        self::assertEquals(23420, $result);
        self::assertEqualsWithDelta(23420, $result, 1.0e-8);
    }

    public function testTIMEVALUEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = TimeValue::fromString('7:30:20');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }

    /**
     * @dataProvider providerUnhappyTIMEVALUE
     */
    public function testTIMEVALUEUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=TIMEVALUE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyTIMEVALUE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for TIMEVALUE() function'],
        ];
    }

    /**
     * @dataProvider providerTimeValueArray
     */
    public function testTimeValueArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TIMEVALUE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTimeValueArray(): array
    {
        return [
            'row vector' => [[[0.04309027777777, 0.5515625, 0.80579861111111]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[0.04309027777777], [0.5515625], [0.80579861111111]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[0.04309027777777, 0.5515625], [0.80579861111111, 0.99998842592592]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}
