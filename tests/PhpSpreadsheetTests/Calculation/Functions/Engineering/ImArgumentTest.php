<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ImArgumentTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIMARGUMENT
     */
    public function testDirectCallToIMARGUMENT(float|int|string $expectedResult, string $arg): void
    {
        $result = ComplexFunctions::IMARGUMENT($arg);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMARGUMENT
     */
    public function testIMARGUMENTAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMARGUMENT({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMARGUMENT
     */
    public function testIMARGUMENTInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMARGUMENT({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMARGUMENT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMARGUMENT.php';
    }

    /**
     * @dataProvider providerUnhappyIMARGUMENT
     */
    public function testIMARGUMENTUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMARGUMENT({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMARGUMENT(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMARGUMENT() function'],
        ];
    }

    public static function providerImArgumentArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [-1.9513027039072615, -1.5707963267948966, -1.1902899496825317],
                    [-2.356194490192345, -1.5707963267948966, -0.7853981633974483],
                    [2.356194490192345, 1.5707963267948966, 0.7853981633974483],
                    [1.9513027039072615, 1.5707963267948966, 1.1902899496825317],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
