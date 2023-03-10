<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Week;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class IsoWeekNumTest extends TestCase
{
    /**
     * @var int
     */
    private $excelCalendar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->excelCalendar = SharedDate::getExcelCalendar();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        SharedDate::setExcelCalendar($this->excelCalendar);
    }

    /**
     * @dataProvider providerISOWEEKNUM
     *
     * @param mixed $expectedResult
     * @param mixed ...$args
     */
    public function testDirectCallToISOWEEKNUM($expectedResult, ...$args): void
    {
        $result = Week::isoWeekNumber(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerISOWEEKNUM
     *
     * @param mixed $expectedResult
     * @param mixed ...$args
     */
    public function testISOWEEKNUMAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=ISOWEEKNUM({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    public function providerISOWEEKNUM(): array
    {
        return require 'tests/data/Calculation/DateTime/ISOWEEKNUM.php';
    }

    /**
     * @dataProvider providerUnhappyISOWEEKNUM
     */
    public function testISOWEEKNUMUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=ISOWEEKNUM({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
    }

    public function providerUnhappyISOWEEKNUM(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for ISOWEEKNUM() function', 2023, 03],
        ];
    }

    /**
     * @dataProvider providerISOWEEKNUM1904
     *
     * @param mixed $expectedResult
     */
    public function testISOWEEKNUMWith1904Calendar($expectedResult, ...$args): void
    {
        SharedDate::setExcelCalendar(SharedDate::CALENDAR_MAC_1904);

        $result = Week::isoWeekNumber(...$args);
        self::assertSame($expectedResult, $result);
    }

    public function providerISOWEEKNUM1904(): array
    {
        return require 'tests/data/Calculation/DateTime/ISOWEEKNUM1904.php';
    }

    /**
     * @dataProvider providerIsoWeekNumArray
     */
    public function testIsoWeekNumArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISOWEEKNUM({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerIsoWeekNumArray(): array
    {
        return [
            'row vector' => [[[52, 23, 29]], '{"2022-01-01", "2022-06-12", "2023-07-22"}'],
            'column vector' => [[[52], [13], [26]], '{"2023-01-01"; "2023-04-01"; "2023-07-01"}'],
            'matrix' => [[[53, 52], [52, 52]], '{"2021-01-01", "2021-12-31"; "2023-01-01", "2023-12-31"}'],
        ];
    }
}
