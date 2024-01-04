<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days360;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class Days360Test extends TestCase
{
    /**
     * @dataProvider providerDAYS360
     */
    public function testDirectCallToDAYS360(mixed $expectedResult, mixed ...$args): void
    {
        $result = Days360::between(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDAYS360
     */
    public function testDAYS360AsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DAYS360({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDAYS360
     */
    public function testDAYS360InWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAYS360({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDAYS360(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYS360.php';
    }

    /**
     * @dataProvider providerUnhappyDAYS360
     */
    public function testDAYS360UnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAYS360({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDAYS360(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DAYS360() function'],
        ];
    }

    public function testDateObject(): void
    {
        $obj1 = new DateTime('2000-3-31');
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame(31, Days::between($obj1, $obj2));
    }

    /**
     * @dataProvider providerDays360Array
     */
    public function testDays360Array(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=DAYS360({$startDate}, {$endDate})";
        } else {
            $formula = "=DAYS360({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDays360Array(): array
    {
        return [
            'row vector #1' => [[[360, 199, -201]], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'column vector #1' => [[[360], [358], [355]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[0, -9], [-224, -360]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}', '"2021-12-31"', null],
            'column vector with methods' => [[[360, 359], [358, 357], [355, 354]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', '{FALSE, TRUE}'],
        ];
    }
}
