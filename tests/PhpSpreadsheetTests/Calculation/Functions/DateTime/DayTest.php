<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class DayTest extends TestCase
{
    private string $compatibilityMode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerDAY
     */
    public function testDirectCallToDAY(mixed $expectedResultExcel, mixed ...$args): void
    {
        $result = DateParts::day(...$args);
        self::assertSame($expectedResultExcel, $result);
    }

    /**
     * @dataProvider providerDAY
     */
    public function testDAYAsFormula(mixed $expectedResultExcel, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DAY({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResultExcel, $result);
    }

    /**
     * @dataProvider providerDAY
     */
    public function testDAYInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAY({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDAY(): array
    {
        return require 'tests/data/Calculation/DateTime/DAY.php';
    }

    /**
     * @dataProvider providerDAYOpenOffice
     */
    public function testDirectCallToDAYOpenOffice(mixed $expectedResultOpenOffice, mixed ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = DateParts::day(...$args);
        self::assertSame($expectedResultOpenOffice, $result);
    }

    /**
     * @dataProvider providerDAYOpenOffice
     */
    public function testDAYAsFormulaOpenOffice(mixed $expectedResultOpenOffice, mixed ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DAY({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResultOpenOffice, $result);
    }

    public static function providerDAYOpenOffice(): array
    {
        return require 'tests/data/Calculation/DateTime/DAYOpenOffice.php';
    }

    /**
     * @dataProvider providerUnhappyDAY
     */
    public function testDAYUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DAY({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDAY(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DAY() function'],
        ];
    }

    public function testDirectCallToDAYWithNull(): void
    {
        $result = DateParts::day(null);
        self::assertSame(0, $result);
    }

    /**
     * @dataProvider providerDayArray
     */
    public function testDayArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DAY({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerDayArray(): array
    {
        return [
            'row vector' => [[[1, 12, 22]], '{"2022-01-01", "2022-06-12", "2023-07-22"}'],
            'column vector' => [[[1], [3], [6]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}'],
            'matrix' => [[[1, 10], [15, 31]], '{"2022-01-01", "2022-01-10"; "2022-08-15", "2022-12-31"}'],
        ];
    }
}
