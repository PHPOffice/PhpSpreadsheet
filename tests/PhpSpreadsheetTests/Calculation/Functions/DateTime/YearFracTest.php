<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class YearFracTest extends TestCase
{
    /**
     * @dataProvider providerYEARFRAC
     */
    public function testDirectCallToYEARFRAC(mixed $expectedResult, mixed ...$args): void
    {
        $result = YearFrac::fraction(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    /**
     * @dataProvider providerYEARFRAC
     */
    public function testYEARFRACAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=YEARFRAC({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);
    }

    /**
     * @dataProvider providerYEARFRAC
     */
    public function testYEARFRACInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=YEARFRAC({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-6);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerYEARFRAC(): array
    {
        return require 'tests/data/Calculation/DateTime/YEARFRAC.php';
    }

    /**
     * @dataProvider providerUnhappyYEARFRAC
     */
    public function testYEARFRACUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=YEARFRAC({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyYEARFRAC(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for YEARFRAC() function'],
            ['Formula Error: Wrong number of arguments for YEARFRAC() function', '2023-03-09'],
        ];
    }

    /**
     * @dataProvider providerYearFracArray
     */
    public function testYearFracArray(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=YEARFRAC({$startDate}, {$endDate})";
        } else {
            $formula = "=YEARFRAC({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerYearFracArray(): array
    {
        return [
            'row vector #1' => [[[1.0, 0.55277777777778, 0.56111111111111]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'column vector #1' => [[[1.0], [0.99444444444445], [0.98611111111111]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[0.002777777777778, 0.027777777777778], [0.625, 1.0]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"', null],
            'column vector with methods' => [[[0.99726027397260, 0.99722222222222], [0.99178082191781, 0.99166666666667], [0.98356164383562, 0.98333333333333]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', '{1, 4}'],
        ];
    }
}
