<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MonthTest extends TestCase
{
    #[DataProvider('providerMONTH')]
    public function testDirectCallToMONTH(mixed $expectedResultExcel, mixed ...$args): void
    {
        $result = DateParts::month(...$args);
        self::assertSame($expectedResultExcel, $result);
    }

    #[DataProvider('providerMONTH')]
    public function testMONTHAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=MONTH({$arguments})";

        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerMONTH')]
    public function testMONTHInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=MONTH({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerMONTH(): array
    {
        return require 'tests/data/Calculation/DateTime/MONTH.php';
    }

    #[DataProvider('providerUnhappyMONTH')]
    public function testMONTHUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=MONTH({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyMONTH(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for MONTH() function'],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerMonthArray')]
    public function testMonthArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MONTH({$array})";
        $result = $calculation->calculateFormula($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerMonthArray(): array
    {
        return [
            'row vector' => [[[1, 6, 1]], '{"2022-01-01", "2022-06-01", "2023-01-01"}'],
            'column vector' => [[[1], [3], [6]], '{"2022-01-01"; "2022-03-01"; "2022-06-01"}'],
            'matrix' => [[[1, 4], [8, 12]], '{"2022-01-01", "2022-04-01"; "2022-08-01", "2022-12-01"}'],
        ];
    }
}
