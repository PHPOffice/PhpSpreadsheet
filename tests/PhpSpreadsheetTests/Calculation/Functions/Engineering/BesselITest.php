<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselI;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BesselITest extends TestCase
{
    const BESSEL_PRECISION = 1E-9;

    /**
     * @dataProvider providerBESSELI
     */
    public function testDirectCallToBESSELI(mixed $expectedResult, mixed ...$args): void
    {
        $result = BesselI::besselI(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELI
     */
    public function testBESSELIAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BESSELI({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELI
     */
    public function testBESSELIInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELI({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBESSELI(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELI.php';
    }

    /**
     * @dataProvider providerUnhappyBESSELI
     */
    public function testBESSELIUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELI({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBESSELI(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BESSELI() function'],
            ['Formula Error: Wrong number of arguments for BESSELI() function', 2023],
        ];
    }

    /**
     * @dataProvider providerBesselIArray
     */
    public function testBesselIArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELI({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    public static function providerBesselIArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1.393725572000487, 1.0634833439946074, 1.0, 1.0156861326120836, 3.2898391723912908],
                    [-0.7146779363262508, -0.25789430328903556, 0.0, 0.12597910862299733, 2.516716242025361],
                    [0.20259567978255663, 0.031906148375295325, 0.0, 0.007853269593280343, 1.276466158815611],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
