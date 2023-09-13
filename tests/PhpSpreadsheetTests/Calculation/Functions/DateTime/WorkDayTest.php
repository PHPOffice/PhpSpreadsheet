<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\WorkDay;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class WorkDayTest extends TestCase
{
    /**
     * @dataProvider providerWORKDAY
     */
    public function testDirectCallToWORKDAY(mixed $expectedResult, mixed ...$args): void
    {
        $result = WorkDay::date(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerWORKDAY
     */
    public function testWORKDAYAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=WORKDAY({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerWORKDAY
     */
    public function testWORKDAYInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=WORKDAY({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerWORKDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/WORKDAY.php';
    }

    /**
     * @dataProvider providerUnhappyWORKDAY
     */
    public function testWORKDAYUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=WORKDAY({$argumentCells})";

        $this->expectException(\PhpOffice\PhpSpreadsheet\Calculation\Exception::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyWORKDAY(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for WORKDAY() function'],
        ];
    }

    /**
     * @dataProvider providerWorkDayArray
     */
    public function testWorkDayArray(array $expectedResult, string $startDate, string $endDays, ?string $holidays): void
    {
        $calculation = Calculation::getInstance();

        if ($holidays === null) {
            $formula = "=WORKDAY({$startDate}, {$endDays})";
        } else {
            $formula = "=WORKDAY({$startDate}, {$endDays}, {$holidays})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerWorkDayArray(): array
    {
        return [
            'row vector #1' => [[[44595, 44596, 44599]], '{"2022-02-01", "2022-02-02", "2022-02-03"}', '2', null],
            'column vector #1' => [[[44595], [44596], [44599]], '{"2022-02-01"; "2022-02-02"; "2022-02-03"}', '2', null],
            'matrix #1' => [[[44595, 44596], [44599, 44600]], '{"2022-02-01", "2022-02-02"; "2022-02-03", "2022-02-04"}', '2', null],
            'row vector #2' => [[[44595, 44596]], '"2022-02-01"', '{2, 3}', null],
            'column vector #2' => [[[44595], [44596]], '"2022-02-01"', '{2; 3}', null],
            'row vector with Holiday' => [[[44596, 44599]], '"2022-02-01"', '{2, 3}', '{"2022-02-02"}'],
            'row vector with Holidays' => [[[44599, 44600]], '"2022-02-01"', '{2, 3}', '{"2022-02-02", "2022-02-03"}'],
        ];
    }
}
