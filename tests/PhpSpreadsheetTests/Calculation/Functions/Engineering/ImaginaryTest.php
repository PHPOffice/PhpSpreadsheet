<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ImaginaryTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToIMAGINARY($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = Complex::IMAGINARY(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     */
    public function testIMAGINARYAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMAGINARY({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMAGINARY
     *
     * @param mixed $expectedResult
     */
    public function testIMAGINARYInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMAGINARY({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMAGINARY(): array
    {
        return require 'tests/data/Calculation/Engineering/IMAGINARY.php';
    }

    /**
     * @dataProvider providerUnhappyIMAGINARY
     */
    public function testIMAGINARYUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMAGINARY({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMAGINARY(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMAGINARY() function'],
        ];
    }

    /**
     * @dataProvider providerImaginaryArray
     */
    public function testImaginaryArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMAGINARY({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImaginaryArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-2.5, -2.5, -2.5],
                    [-1.0, -1.0, -1.0],
                    [1.0, 1.0, 1.0],
                    [2.5, 2.5, 2.5],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
