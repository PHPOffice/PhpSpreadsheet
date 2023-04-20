<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testDirectCallToCOMPLEX($expectedResult, ...$args): void
    {
        /** @scrutinizer ignore-call */
        $result = Complex::complex(...$args);
        self::assertSame($expectedResult, $result);
    }

    private function trimIfQuoted(string $value): string
    {
        return trim($value, '"');
    }

    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEXAsFormula($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=COMPLEX({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $this->trimIfQuoted((string) $result));
    }

    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEXInWorksheet($expectedResult, ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=COMPLEX({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerCOMPLEX(): array
    {
        return require 'tests/data/Calculation/Engineering/COMPLEX.php';
    }

    /**
     * @dataProvider providerComplexArray
     */
    public function testComplexArray(array $expectedResult, string $real, string $imaginary): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMPLEX({$real}, {$imaginary})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerComplexArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-2.5-2.5i', '-1-2.5i', '-2.5i', '1-2.5i', '2.5-2.5i'],
                    ['-2.5-i', '-1-i', '-i', '1-i', '2.5-i'],
                    ['-2.5', '-1', '0.0', '1', '2.5'],
                    ['-2.5+i', '-1+i', 'i', '1+i', '2.5+i'],
                    ['-2.5+2.5i', '-1+2.5i', '2.5i', '1+2.5i', '2.5+2.5i'],
                ],
                '{-2.5, -1, 0, 1, 2.5}',
                '{-2.5; -1; 0; 1; 2.5}',
            ],
        ];
    }
}
