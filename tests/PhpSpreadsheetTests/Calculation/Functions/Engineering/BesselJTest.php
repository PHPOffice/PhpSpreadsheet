<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselJ;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BesselJTest extends TestCase
{
    const BESSEL_PRECISION = 1E-8;

    /**
     * @dataProvider providerBESSELJ
     */
    public function testDirectCallToBESSELJ(mixed $expectedResult, mixed ...$args): void
    {
        $result = BesselJ::besselJ(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELJ
     */
    public function testBESSELJAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BESSELJ({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELJ
     */
    public function testBESSELJInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELJ({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBESSELJ(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELJ.php';
    }

    /**
     * @dataProvider providerUnhappyBESSELJ
     */
    public function testBESSELJUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELJ({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBESSELJ(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BESSELJ() function'],
            ['Formula Error: Wrong number of arguments for BESSELJ() function', 2023],
        ];
    }

    /**
     * @dataProvider providerBesselJArray
     */
    public function testBesselJArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELJ({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBesselJArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [0.6711327417644983, 0.9384698074235406, 1.00000000283141, 0.9844359313618615, -0.04838377582675685],
                    [-0.4982890574931824, -0.24226845767957006, 0.0, 0.12402597733693042, 0.49709410250442176],
                    [0.15934901834766313, 0.03060402345868265, 0.0, 0.007771889285962677, 0.44605905783029426],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
