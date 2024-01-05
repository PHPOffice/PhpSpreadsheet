<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ImRealTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMREAL
     */
    public function testDirectCallToIMREAL(float|int|string $expectedResult, float|int|string $arg): void
    {
        $result = Complex::IMREAL((string) $arg);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMREAL
     */
    public function testIMREALAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMREAL({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMREAL
     */
    public function testIMREALInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMREAL({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMREAL(): array
    {
        return require 'tests/data/Calculation/Engineering/IMREAL.php';
    }

    /**
     * @dataProvider providerUnhappyIMREAL
     */
    public function testIMREALUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMREAL({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMREAL(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMREAL() function'],
        ];
    }

    /**
     * @dataProvider providerImRealArray
     */
    public function testImRealArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMREAL({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImRealArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                    [-1.0, 0.0, 1.0],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
