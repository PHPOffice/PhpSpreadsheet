<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
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

    /**
     * @dataProvider providerDATE
     */
    public function testDirectCallToDATE(float|string $expectedResult, int|string $year, float|int|string $month, float|int|string $day): void
    {
        $result = Date::fromYMD($year, $month, $day);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDATE
     */
    public function testDATEAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DATE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerDATE
     */
    public function testDATEInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDATE(): array
    {
        return require 'tests/data/Calculation/DateTime/DATE.php';
    }

    /**
     * @dataProvider providerUnhappyDATE
     */
    public function testDATEUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDATE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DATE() function', 2023, 3],
        ];
    }

    public function testDATEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = Date::fromYMD(2012, 1, 31); // 32-bit safe
        self::assertEquals(1327968000, $result);
    }

    public function testDATEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);

        $result = Date::fromYMD(2012, 1, 31);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEWith1904Calendar(): void
    {
        SharedDate::setExcelCalendar(SharedDate::CALENDAR_MAC_1904);

        $result = Date::fromYMD(1918, 11, 11);
        self::assertEquals($result, 5428);

        $result = Date::fromYMD(1901, 1, 31);
        self::assertEquals($result, ExcelError::NAN());
    }

    /**
     * @dataProvider providerDateArray
     */
    public function testDateArray(array $expectedResult, string $year, string $month, string $day): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DATE({$year}, {$month}, {$day})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDateArray(): array
    {
        return [
            'row vector year' => [[[44197, 44562, 44927]], '{2021,2022,2023}', '1', '1'],
            'column vector year' => [[[44197], [44562], [44927]], '{2021;2022;2023}', '1', '1'],
            'matrix year' => [[[43831.00, 44197], [44562, 44927]], '{2020,2021;2022,2023}', '1', '1'],
            'row vector month' => [[[44562, 44652, 44743, 44835]], '2022', '{1, 4, 7, 10}', '1'],
            'column vector month' => [[[44562], [44652], [44743], [44835]], '2022', '{1; 4; 7; 10}', '1'],
            'matrix month' => [[[44562, 44652], [44743, 44835]], '2022', '{1, 4; 7, 10}', '1'],
            'row vector day' => [[[44561, 44562]], '2022', '1', '{0,1}'],
            'column vector day' => [[[44561], [44562]], '2022', '1', '{0;1}'],
            'vectors year and month' => [
                [
                    [44197, 44287, 44378, 44470],
                    [44562, 44652, 44743, 44835],
                    [44927, 45017, 45108, 45200],
                ],
                '{2021;2022;2023}',
                '{1, 4, 7, 10}',
                '1',
            ],
            'vectors year and day' => [
                [
                    [44196, 44197],
                    [44561, 44562],
                    [44926, 44927],
                ],
                '{2021;2022;2023}',
                '1',
                '{0,1}',
            ],
            'vectors month and day' => [
                [
                    [44561, 44562],
                    [44651, 44652],
                    [44742, 44743],
                    [44834, 44835],
                ],
                '2022',
                '{1; 4; 7; 10}',
                '{0,1}',
            ],
            'matrices year and month' => [
                [
                    [43831, 44287],
                    [44743, 45200],
                ],
                '{2020, 2021; 2022, 2023}',
                '{1, 4; 7, 10}',
                '1',
            ],
        ];
    }

    /**
     * @dataProvider providerDateArrayException
     */
    public function testDateArrayException(string $year, string $month, string $day): void
    {
        $calculation = Calculation::getInstance();

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Formulae with more than two array arguments are not supported');

        $formula = "=DATE({$year}, {$month}, {$day})";
        $calculation->_calculateFormulaValue($formula);
    }

    public static function providerDateArrayException(): array
    {
        return [
            'matrix arguments with 3 array values' => [
                '{2020, 2021; 2022, 2023}',
                '{1, 4; 7, 10}',
                '{0,1}',
            ],
        ];
    }
}
