<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\NetworkDays;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class NetworkDaysTest extends TestCase
{
    /**
     * @dataProvider providerNETWORKDAYS
     */
    public function testDirectCallToNETWORKDAYS(mixed $expectedResult, mixed ...$args): void
    {
        $result = NetworkDays::count(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerNETWORKDAYS
     */
    public function testNETWORKDAYSAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=NETWORKDAYS({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerNETWORKDAYS
     */
    public function testNETWORKDAYSInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=NETWORKDAYS({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerNETWORKDAYS(): array
    {
        return require 'tests/data/Calculation/DateTime/NETWORKDAYS.php';
    }

    /**
     * @dataProvider providerUnhappyNETWORKDAYS
     */
    public function testNETWORKDAYSUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=NETWORKDAYS({$argumentCells})";

        $this->expectException(\PhpOffice\PhpSpreadsheet\Calculation\Exception::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyNETWORKDAYS(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for NETWORKDAYS() function'],
            ['Formula Error: Wrong number of arguments for NETWORKDAYS() function', '2001-01-01'],
        ];
    }

    /**
     * @dataProvider providerNetWorkDaysArray
     */
    public function testNetWorkDaysArray(array $expectedResult, string $startDate, string $endDays, ?string $holidays): void
    {
        $calculation = Calculation::getInstance();

        if ($holidays === null) {
            $formula = "=NETWORKDAYS({$startDate}, {$endDays})";
        } else {
            $formula = "=NETWORKDAYS({$startDate}, {$endDays}, {$holidays})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNetWorkDaysArray(): array
    {
        return [
            'row vector #1' => [[[234, 233, 232]], '{"2022-02-01", "2022-02-02", "2022-02-03"}', '"2022-12-25"', null],
            'column vector #1' => [[[234], [233], [232]], '{"2022-02-01"; "2022-02-02"; "2022-02-03"}', '"2022-12-25"', null],
            'matrix #1' => [[[234, 233], [232, 231]], '{"2022-02-01", "2022-02-02"; "2022-02-03", "2022-02-04"}', '"2022-12-25"', null],
            'row vector #2' => [[[234, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', null],
            'column vector #2' => [[[234], [-27]], '"2022-02-01"', '{"2022-12-25"; "2021-12-25"}', null],
            'row vector with Holiday' => [[[233, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', '{"2022-02-02"}'],
            'row vector with Holidays' => [[[232, -27]], '"2022-02-01"', '{"2022-12-25", "2021-12-25"}', '{"2022-02-02", "2022-02-03"}'],
        ];
    }
}
