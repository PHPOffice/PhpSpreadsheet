<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ComplexFunctions;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ImAbsTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-12;

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToIMABS($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = ComplexFunctions::IMABS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     */
    public function testIMABSAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=IMABS({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);
    }

    /**
     * @dataProvider providerIMABS
     *
     * @param mixed $expectedResult
     */
    public function testIMABSInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMABS({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, self::COMPLEX_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerIMABS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMABS.php';
    }

    /**
     * @dataProvider providerUnhappyIMABS
     */
    public function testIMABSUnhappyPath(string $expectedException, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=IMABS({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyIMABS(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for IMABS() function'],
        ];
    }

    /**
     * @dataProvider providerImAbsArray
     */
    public function testImAbsArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMABS({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerImAbsArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [2.692582403567252, 2.5, 2.692582403567252],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [1.4142135623730951, 1.0, 1.4142135623730951],
                    [2.692582403567252, 2.5, 2.692582403567252],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
