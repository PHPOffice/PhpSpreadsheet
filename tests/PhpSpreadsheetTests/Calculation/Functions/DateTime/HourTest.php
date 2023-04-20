<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeParts;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class HourTest extends TestCase
{
    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToHOUR($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = TimeParts::hour(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     */
    public function testHOURAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=HOUR({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     */
    public function testHOURInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HOUR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerHOUR(): array
    {
        return require 'tests/data/Calculation/DateTime/HOUR.php';
    }

    /**
     * @dataProvider providerUnhappyHOUR
     */
    public function testHOURUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=HOUR({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyHOUR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for HOUR() function'],
        ];
    }

    /**
     * @dataProvider providerHourArray
     */
    public function testHourArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=HOUR({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerHourArray(): array
    {
        return [
            'row vector' => [[[1, 13, 19]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15", "2022-02-09 19:20:21"}'],
            'column vector' => [[[1], [13], [19]], '{"2022-02-09 01:02:03"; "2022-02-09 13:14:15"; "2022-02-09 19:20:21"}'],
            'matrix' => [[[1, 13], [19, 23]], '{"2022-02-09 01:02:03", "2022-02-09 13:14:15"; "2022-02-09 19:20:21", "1999-12-31 23:59:59"}'],
        ];
    }
}
