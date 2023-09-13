<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BesselY;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class BesselYTest extends TestCase
{
    const BESSEL_PRECISION = 1E-12;

    /**
     * @dataProvider providerBESSELY
     */
    public function testDirectCallToBESSELY(mixed $expectedResult, mixed ...$args): void
    {
        $result = BesselY::besselY(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELY
     */
    public function testBESSELYAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=BESSELY({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);
    }

    /**
     * @dataProvider providerBESSELY
     */
    public function testBESSELYInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELY({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::BESSEL_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBESSELY(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELY.php';
    }

    /**
     * @dataProvider providerUnhappyBESSELY
     */
    public function testBESSELYUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=BESSELY({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyBESSELY(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for BESSELY() function'],
            ['Formula Error: Wrong number of arguments for BESSELY() function', 2023],
        ];
    }

    /**
     * @dataProvider providerBesselYArray
     */
    public function testBesselYArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELY({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerBesselYArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-3.005455650891885, -0.9315730314941618, -0.44451873376270784, 0.25821685699105446, 0.4980703584466886],
                    [-63.67859624529592, -2.7041052277866418, -1.4714723918672943, -0.5843640364184131, 0.14591813750831284],
                    [-12732.713793408293, -20.701268790798974, -5.441370833706469, -1.1931993152605154, -0.3813358484400383],
                ],
                '{0.01, 0.25, 0.5, 1.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
