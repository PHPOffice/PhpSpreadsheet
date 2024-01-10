<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class DaysTest extends TestCase
{
    /**
     * @dataProvider providerDAYS
     */
    public function testDirectCallToDAYS(int|string $expectedResult, int|string $date1, int|string $date2): void
    {
        $result = Days::between($date1, $date2);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDAYS
     */
    public function testDAYSAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DAYS({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDAYS
     */
    public function testDAYSInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAYS({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDAYS(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS.php';
    }

    /**
     * @dataProvider providerUnhappyDAYS
     */
    public function testDAYSUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAYS({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDAYS(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DAYS() function', '2023-04-01'],
        ];
    }

    public function testDateObject(): void
    {
        $obj1 = new DateTime('2000-3-31');
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame(31, Days::between($obj1, $obj2));
    }

    /**
     * @dataProvider providerDaysArray
     */
    public function testDaysArray(array $expectedResult, string $startDate, string $endDate): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DAYS({$startDate}, {$endDate})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDaysArray(): array
    {
        return [
            'row vector #1' => [[[-364, -202, 203]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"'],
            'column vector #1' => [[[-364], [-362], [-359]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"'],
            'matrix #1' => [[[1, 10], [227, 365]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"'],
        ];
    }
}
