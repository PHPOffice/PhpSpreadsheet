<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselK;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BesselKTest extends TestCase
{
    const BESSEL_PRECISION = 1E-12;

    /**
     * @dataProvider providerBESSELK
     */
    public function testDirectCallToBESSELK(mixed $expectedResult, mixed ...$args): void
    {
        $result = BesselK::besselK(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELK
     */
    public function testBESSELKAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BESSELK({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELK
     */
    public function testBESSELKInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELK({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBESSELK(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELK.php';
    }

    /**
     * @dataProvider providerUnhappyBESSELK
     */
    public function testBESSELKUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELK({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBESSELK(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BESSELK() function'],
            ['Formula Error: Wrong number of arguments for BESSELK() function', 2023],
        ];
    }

    /**
     * @dataProvider providerBesselKArray
     */
    public function testBesselKArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELK({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBesselKArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [4.721244734980139, 1.5415067364690132, 0.9244190350213235, 0.2976030874538336, 0.06234755419101918],
                    [99.97389411857176, 3.747025980669556, 1.6564411280110791, 0.4021240820149834, 0.07389081565026694],
                    [19999.500068449335, 31.517714581825462, 7.5501835470656395, 0.9410016186778072, 0.12146020671123273],
                ],
                '{0.01, 0.25, 0.5, 1.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
