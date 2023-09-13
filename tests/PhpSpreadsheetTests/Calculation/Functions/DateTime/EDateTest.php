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

class EDateTest extends TestCase
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
     * @dataProvider providerEDATE
     */
    public function testDirectCallToEDATE(mixed $expectedResult, mixed ...$args): void
    {
        $result = Month::adjust(...$args);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerEDATE
     */
    public function testEDATEAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=EDATE({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerEDATE
     */
    public function testEDATEInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=EDATE({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerEDATE(): array
    {
        return require 'tests/data/Calculation/DateTime/EDATE.php';
    }

    /**
     * @dataProvider providerUnhappyEDATE
     */
    public function testEDATEUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=EDATE({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyEDATE(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for EDATE() function', null],
            ['Formula Error: Wrong number of arguments for EDATE() function', 22669],
        ];
    }

    public function testEDATEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = Month::adjust('2012-1-26', -1);
        self::assertEquals(1324857600, $result);
        self::assertEqualsWithDelta(1324857600, $result, 1E-8);
    }

    public function testEDATEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = Month::adjust('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }

    /**
     * @dataProvider providerEDateArray
     */
    public function testEDateArray(array $expectedResult, string $dateValues, string $methods): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=EDATE({$dateValues}, {$methods})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerEDateArray(): array
    {
        return [
            'row vector #1' => [[[44593, 44632, 45337]], '{"2022-01-01", "2022-02-12", "2024-01-15"}', '1'],
            'column vector #1' => [[[44593], [44632], [45337]], '{"2022-01-01"; "2022-02-12"; "2024-01-15"}', '1'],
            'matrix #1' => [[[44593, 44632], [44652, 45343]], '{"2022-01-01", "2022-02-12"; "2022-03-01", "2024-01-21"}', '1'],
            'row vector #2' => [[[44573, 44604, 44632]], '"2022-02-12"', '{-1, 0, 1}'],
            'column vector #2' => [[[44573], [44604], [44632]], '"2022-02-12"', '{-1; 0; 1}'],
            'matrix #2' => [[[44573, 44604], [44632, 45334]], '"2022-02-12"', '{-1, 0; 1, 24}'],
        ];
    }
}
