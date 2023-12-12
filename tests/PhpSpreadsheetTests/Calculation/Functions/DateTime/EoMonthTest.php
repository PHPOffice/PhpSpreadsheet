<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class EoMonthTest extends TestCase
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
     * @dataProvider providerEOMONTH
     */
    public function testDirectCallToEOMONTH(mixed $expectedResult, mixed ...$args): void
    {
        $result = Month::lastDay(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerEOMONTH
     */
    public function testEOMONTHAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=EOMONTH({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerEOMONTH
     */
    public function testEOMONTHInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=EOMONTH({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerEOMONTH(): array
    {
        return require 'tests/data/Calculation/DateTime/EOMONTH.php';
    }

    /**
     * @dataProvider providerUnhappyEOMONTH
     */
    public function testEOMONTHUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=EOMONTH({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyEOMONTH(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for EOMONTH() function'],
            ['Formula Error: Wrong number of arguments for EOMONTH() function', 22669],
        ];
    }

    public function testEOMONTHtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = Month::lastDay('2012-1-26', -1);
        self::assertEquals(1325289600, $result);
    }

    public function testEOMONTHtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = Month::lastDay('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertSame($result->format('d-M-Y'), '31-Dec-2011');
    }

    /**
     * @dataProvider providerEoMonthArray
     */
    public function testEoMonthArray(array $expectedResult, string $dateValues, string $methods): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EOMONTH({$dateValues}, {$methods})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerEoMonthArray(): array
    {
        return [
            'row vector #1' => [[[44620, 44651, 45351]], '{"2022-01-01", "2022-02-12", "2024-01-15"}', '1'],
            'column vector #1' => [[[44620], [44651], [45351]], '{"2022-01-01"; "2022-02-12"; "2024-01-15"}', '1'],
            'matrix #1' => [[[44620, 44651], [44681, 45351]], '{"2022-01-01", "2022-02-12"; "2022-03-01", "2024-01-21"}', '1'],
            'row vector #2' => [[[44592, 44620, 44651]], '"2022-02-12"', '{-1, 0, 1}'],
            'column vector #2' => [[[44592], [44620], [44651]], '"2022-02-12"', '{-1; 0; 1}'],
            'matrix #2' => [[[44592, 44620], [44651, 45351]], '"2022-02-12"', '{-1, 0; 1, 24}'],
        ];
    }
}
